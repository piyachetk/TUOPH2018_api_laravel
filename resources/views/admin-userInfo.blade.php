@extends('layouts.app')

@php
    $userId = request()->get('code');
    $user = \App\Account::where('id', '=', $userId)->first();
@endphp

@section("title")
    {{ $user != null ? $user->firstName . ' ' . $user->lastName : 'ไม่พบผู้ใช้' }}
@endsection

@section("content")
<div class="container">
    <div class="section">

        @if(session()->has('success'))
            <div class="z-depth-1 card-panel green white-text" style="max-width:800px;margin: 3rem auto auto;">
                {{ session()->get('success') }}
            </div>
            <br/>
        @endif

        @if(session()->has('error'))
            <div class="z-depth-1 card-panel red white-text" style="max-width:800px;margin: 3rem auto auto;">
                {{ session()->get('error') }}
            </div>
            <br/>
        @endif

        @if($user != null)

            <h4>ข้อมูลบัญชีผู้ใช้ ({{ $user->id }})</h4>
            <br/>
            <div class="row">
                <div class="col s12 m3">
                    <img src="{{ $user->picture }}" style="max-width: 100%; width: 100%;"/>
                </div>
                <div class="col s12 m9">

                    <div class="z-depth-1 card-panel white" style="max-width:800px;">
                        @php
                            switch($user->prefix){
                                case 'mr':
                                    $prefix = 'นาย';
                                    break;
                                case 'miss':
                                    $prefix = 'นางสาว';
                                    break;
                                case 'mrs':
                                    $prefix = 'นาง';
                                    break;
                                case 'master-boy':
                                    $prefix = 'เด็กชาย';
                                    break;
                                case 'master-girl':
                                    $prefix = 'เด็กหญิง';
                                    break;
                                default:
                                    $prefix = '';
                                    break;
                            }
                        @endphp

                        <h5>{{ $prefix . $user->firstName . ' ' . $user->lastName }}</h5>

                        @if(isset($user->email))
                            <b>{{ $user->email }}</b>
                        @endif
                    </div>

                    <div class="z-depth-1 card-panel white" style="max-width:800px;">
                        @if($user->registered)
                            <b class="green-text">ลงทะเบียนเรียบร้อยแล้ว</b>
                        @else
                            <b class="red-text">ยังไม่ได้ลงทะเบียน</b>
                        @endif
                    </div>

                    <div class="z-depth-1 card-panel white" style="max-width:800px;">
                        @php
                            switch($user->type){
                                case 'student':
                                    $type = 'นักเรียน';
                                    break;
                                case 'teacher':
                                    $type = 'ครู/อาจารย์';
                                    break;
                                case 'student-college':
                                    $type = 'นักศึกษา';
                                    break;
                                case 'guardian':
                                    $type = 'ผู้ปกครอง';
                                    break;
                            }
                        @endphp

                        <p><b>ประเภทบัญชี: </b>{{ $type }}</p>

                        @if(($user->type == 'student' || $user->type == 'teacher') && isset($user->school))
                            <p><b>โรงเรียน: </b>{{ $user->school }}</p>
                        @endif

                        @if($user->type == 'student' && isset($user->studentYear))
                            @php
                                switch($user->studentYear){
                                    case 'p1-3':
                                        $year = 'ประถมศึกษาตอนต้น';
                                        break;
                                    case 'p4-6':
                                        $year = 'ประถมศึกษาตอนปลาย';
                                        break;
                                    case 'm1':
                                        $year = 'มัธยมศึกษาปีที่ 1';
                                        break;
                                    case 'm2':
                                        $year = 'มัธยมศึกษาปีที่ 2';
                                        break;
                                    case 'm3':
                                        $year = 'มัธยมศึกษาปีที่ 3';
                                        break;
                                    case 'm4':
                                        $year = 'มัธยมศึกษาปีที่ 4';
                                        break;
                                    case 'm5':
                                        $year = 'มัธยมศึกษาปีที่ 5';
                                        break;
                                    case 'm6':
                                        $year = 'มัธยมศึกษาปีที่ 6';
                                        break;
                                }
                            @endphp
                            <p><b>ระดับชั้น: </b>{{ $year }}</p>
                        @endif
                    </div>

                    <div class="z-depth-1 card-panel white" style="max-width:800px;">
                        <p><b>คะแนนแต้ม: </b>{{ $user->points }}</p>
                        <p><b>สิ่งที่สนใจ: </b>{{ implode(', ', $user->interests) }}</p>
                    </div>
                    <div class="row">
                        @if($user->registered)
                            <div class="col s12" style="margin-bottom: 8px;">
                                @if(!$user->receivedCert)
                                    <a href="{{ url('/admin/giveCert/' . $userId) }}" class="btn waves-effect green fullWidth">ยืนยันการให้เกียรติบัตร</a>
                                @else
                                    <a href="{{ url('/admin/ungiveCert/' . $userId) }}" class="btn waves-effect orange fullWidth">ยกเลิกการให้เกียรติบัตร</a>
                                @endif
                            </div>
                        @endif
                        <div class="col s12">
                            @if(substr($user->ref_no, 0, 3) === "fb:")
                                <a href="https://www.facebook.com/{{ substr($user->ref_no, 3) }}" class="btn waves-effect blue fullWidth">Facebook</a>
                            @elseif(substr($user->ref_no, 0, 7) === "google:")
                                <a href="https://plus.google.com/{{ substr($user->ref_no, 7) }}" class="btn waves-effect red fullWidth">Google+</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <br/>
        @else
                <h4>ไม่พบผู้ใช้</h4>
                <br/>
        @endif

    </div>
    <br><br>
</div>
@endsection
