<?php

namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use \App\Account;
use \App\Booth;
use \Exception;

class AccountController extends Controller
{
    public static function httpGet($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);

        curl_close($ch);
        return $output;
    }

    public static function httpPost($url, $params)
    {
        $postData = '';
        //create name value pairs seperated by &
        foreach ($params as $k => $v) {
            $postData .= $k . '=' . $v . '&';
        }
        $postData = rtrim($postData, '&');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, count($postData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $output = curl_exec($ch);

        curl_close($ch);
        return $output;

    }

    public static function formatAccessToken($access_token){
        $json = json_decode(base64_decode($access_token), true);

        $userId = $json['id'];

        if (!isset($userId) || $userId == null){
            return null;
        }

        $hash = $json['hash'];

        if (!isset($hash) || $hash == null){
            return null;
        }

        return $json;
    }

    public static function getUserDataFromAccessToken($access_token){
        try {
            $json = self::formatAccessToken($access_token);

            $user = Account::where('id', '=', $json['id'])->first();

            if (self::getUserHash($user) == $json['hash']){
                return $user;
            }
            else{
                return null;
            }
        }
        catch(Exception $ex){
            return null;
        }
    }

    public static function getUserHash($user){
        if ($user == null){
            return null;
        }

        $userId = $user->id;

        $hash = crypt($userId, $user->created_at);

        return $hash;
    }

    public static function getAccessToken(Request $request){
        //Assuming that this could be the user's first time
        try {
            $type = $request->get('type');
            switch ($type) {
                case 'fb':
                    $data = self::getUserDataFromFacebook($request->get('access_token'));
                    break;
                case 'google':
                    $data = self::getUserDataFromGoogle($request->get('access_token'));
                    break;
                default:
                    return response()->json([
                        'error' => [
                            'message' => 'Invalid Data Submitted',
                            'code' => 101
                        ]
                    ]);
            }

            $user = Account::where('ref_no', '=', $type . ':' . $data['id'])->first();
            if ($user == null) {
                $user = Account::create([
                    //'id' => $userData['id'], We don't create IDs. We let the system increment and assign stuff.
                    'firstName' => array_key_exists('first_name', $data) ? $data['first_name'] : null,
                    'lastName' => array_key_exists('last_name', $data) ? $data['last_name'] : null,
                    'email' => array_key_exists('email', $data) ? $data['email'] : null,
                    'picture' => array_key_exists('picture', $data) ? $data['picture']['data']['url'] : null,
                    'ref_no' => $type . ':' . $data['id'],
                    'scanned' => [],
                    'interests' => []
                ]);
            } else {
                $user->fill([
                    'picture' => array_key_exists('picture', $data) ? $data['picture']['data']['url'] : null
                ]);
                $user->save();
            }

            $new_token = base64_encode(json_encode([
                'id' => $user->id,
                'hash' => self::getUserHash($user)
            ]));

            return response()->json([
                'access_token' => $new_token
            ]);
        }
        catch(Exception $ex){
            return response()->json([
                'error' => [
                    'message' => 'Invalid Data Submitted',
                    'code' => 101
                ]
            ]);
        }
    }

    public static function getUserDataFromFacebook($access_token)
    {
        $result = self::httpGet('https://graph.facebook.com/me?fields=id,first_name,last_name,email,picture&access_token=' . $access_token);
        $json = json_decode($result, true);
        if (array_key_exists('error', $json)) {
            return null;
        }
        $json['picture']['data']['url'] = 'https://graph.facebook.com/' . $json['id'] . '/picture?type=large&width=720&height=720';
        return $json;
    }

    public static function getUserDataFromGoogle($access_token)
    {
        $result = self::httpGet('https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token=' . $access_token);
        $json = json_decode($result, true);
        if (array_key_exists('error', $json)){
            return null;
        }

        $array = [];

        $array['id'] = $json['id'];
        $array['first_name'] = $json['given_name'];
        $array['last_name'] = $json['family_name'];

        $array['picture'] = [
            'data' => [
                'url' => $json['picture']
            ]
        ];

        $array['email'] = $json['email'];

        return $array;
    }

    public function me(Request $request)
    {
        try {
            $access_token = $request->get('access_token');

            $userData = self::formatAccessToken($access_token);

            if ($userData == null) {
                return response()->json([
                    'error' => [
                        'message' => 'Invalid Access Token',
                        'code' => 100
                    ]
                ]);
            }

            $user = Account::where('id', '=', $userData['id'])->first();

            //'User Not Found' error is too specific.
            if (self::getUserHash($user) != $userData['hash']) { //If $user is null, it won't pass here anyway
                return response()->json([
                    'error' => [
                        'message' => 'Invalid Access Token',
                        'code' => 100
                    ]
                ]);
            }

            return response()->json($user->toArray());
        } catch(Exception $ex){
            return response()->json([
                'error' => [
                    'message' => 'Invalid Access Token',
                    'code' => 100
                ]
            ]);
        }
    }

    public function register(Request $request)
    {
        try {
            $rawData = $request->json()->all();

            $access_token = $rawData['access_token'];

            $user = self::getUserDataFromAccessToken($access_token);

            if ($user == null) {
                return response()->json([
                    'error' => [
                        'message' => 'Invalid Access Token',
                        'code' => 100
                    ]
                ]);
            }

            $data = [];
            $data['registered'] = true;
            $data['firstName'] = $rawData['firstName'];
            $data['lastName'] = $rawData['lastName'];
            $data['email'] = $rawData['email'];
            $data['type'] = $rawData['accountType'];
            $data['school'] = ($rawData['accountType'] == 'student' || $rawData['accountType'] == 'teacher') ? $rawData['schoolName'] : null;
            sort($rawData['interests']);
            $data['interests'] = $rawData['interests'];
            $data['prefix'] = $rawData['prefix'];
            $data['studentYear'] = ($rawData['accountType'] == 'student') ? $rawData['studentYear'] : null;

            $user->fill($data);
            $user->save();

            return response()->json([
                'code' => 200
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'error' => [
                    'message' => 'Invalid Data Submitted',
                    'code' => 101
                ]
            ]);
        }
    }

    public function getBoothForAdmin(Request $request)
    {
        $access_token = $request->get('access_token');

        $user = self::getUserDataFromAccessToken($access_token);

        if ($user == null) {
            return response()->json([
                'error' => [
                    'message' => 'Invalid Access Token',
                    'code' => 100
                ]
            ]);
        }

        $list = [];

        $booths = Booth::all();

        foreach ($booths as $booth) {
            if (in_array($user->id, $booth->admin)) {
                //Is admin of this booth
                $list[] = $booth->makeVisible(['points', 'admin', 'scanCount'])->toArray();
            }
        }

        return response()->json($list);
    }

    private static function getBoothObject($boothId){
        return Booth::where('id', '=', $boothId)->first();
    }

    private static function getBoothObjectAsAdmin($user , $boothId){
        $booth = Booth::where('id', '=', $boothId)->first();

        if ($booth != null && in_array($user->id, $booth->admin)) {
            return $booth;
        }
        else{
            return null;
        }
    }

    public function scan(Request $request)
    {
        try {

            $rawData = $request->json()->all();

            $access_token = $rawData['access_token'];

            $user = self::getUserDataFromAccessToken($access_token);

            if ($user == null) {
                return response()->json([
                    'error' => [
                        'message' => 'Invalid Access Token',
                        'code' => 100
                    ]
                ]);
            }

            $boothId = $rawData['boothId'];
            $booth = self::getBoothObjectAsAdmin($user, $boothId);

            //Make sure the ID of the booth is valid and the user is really an admin of it
            if ($booth == null){
                return response()->json([
                    'error' => [
                        'message' => 'Insufficient Privileges or Booth Not Found',
                        'code' => 301
                    ]
                ]);
            }

            $targetUserId = $rawData['targetUserId'];
            $targetUser = Account::where('id', '=', $targetUserId)->first();

            //Make sure ID of the target user is valid
            if ($targetUser == null) {
                return response()->json([
                    'error' => [
                        'message' => 'Target User Does Not Exist',
                        'code' => 302
                    ]
                ]);
            }

            //Make sure the target user has not been scanned yet
            if (in_array($boothId, $targetUser->scanned)) {
                return response()->json([
                    'error' => [
                        'message' => 'Target User Has Already Been Scanned',
                        'code' => 303
                    ]
                ]);
            }

            $scanned = $targetUser->scanned;
            $scanned[] = $boothId;
            $targetUser->scanned = $scanned;

            $targetUser->points += $booth->points;
            $targetUser->save();

            $booth->scanCount += 1;
            $booth->save();

            return response()->json([
                'code' => 200
            ]);

        } catch (Exception $ex) {
            return response()->json([
                'error' => [
                    'message' => 'Invalid Scanned Data',
                    'code' => 300
                ]
            ]);
        }
    }

    public function getAllBooths(Request $request){
        if ($request->has('tag')) {
            $tag = $request->get('tag');

            $all = Booth::all();

            $booths = [];
            foreach($all as $booth){
                if (in_array($tag, $booth->tags)){
                    $booths[] = $booth->toArray();
                }
            }

            return response()->json($booths);
        }
        return response()->json(Booth::all()->toArray());
    }

    public function getAllTags(Request $request){
        $booths = Booth::all();
        $tags = [];
        foreach($booths as $booth){
            foreach($booth->tags as $tag){
                if (!in_array($tag, $tags)){
                    $tags[] = $tag;
                }
            }
        }
        sort($tags);
        return response()->json($tags);
    }

    public function editAdmin(Request $request)
    {
        try {
            $rawData = $request->json()->all();

            $access_token = $rawData['access_token'];

            $user = self::getUserDataFromAccessToken($access_token);

            if ($user == null) {
                return response()->json([
                    'error' => [
                        'message' => 'Invalid Access Token',
                        'code' => 100
                    ]
                ]);
            }

            $boothId = $rawData['boothId'];
            $booth = self::getBoothObjectAsAdmin($user, $boothId);

            //Make sure the ID of the booth is valid and the user is really an admin of it
            if ($booth == null){
                return response()->json([
                    'error' => [
                        'message' => 'Insufficient Privileges or Booth Not Found',
                        'code' => 301
                    ]
                ]);
            }

            $data = [];
            $data['name'] = $rawData['name'];
            $data['description'] = $rawData['description'];
            $data['type'] = $rawData['type'];
            $data['preview'] = $rawData['preview'];
            $data['picture'] = $rawData['picture'];
            $data['location'] = $rawData['location'];
            $data['tags'] = $rawData['tags'];
            $data['time'] = $rawData['type'] == 'show' || $rawData['type'] == 'concert' || $rawData['type'] == 'competition' ? (is_null($rawData['time']) ? '' : $rawData['time']) : null;
            sort($data['tags']);

            $booth->fill($data);
            $booth->save();

            return response()->json([
                'code' => 200
            ]);

        } catch (Exception $ex) {
            return response()->json([
                'error' => [
                    'message' => 'Invalid Data Submitted',
                    'code' => 101
                ]
            ]);
        }
    }
}