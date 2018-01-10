@extends('layouts.app')

@section("title")
    เข้าสู่ระบบ
@endsection

@section("content")
<div class="container">
    <div class="section">

        @if(session()->has('error'))
            <div class="z-depth-1 card-panel red white-text" style="max-width:800px;margin: 3rem auto auto;">
                {{ session()->get('error') }}
            </div>
        @endif

        <div class="z-depth-1 card-panel white" style="max-width:800px;margin: 3rem auto auto;">
            <h5 class="center">Single Password Authentication</h5>

            <form method="POST" action="/admin/login">
                <div class="input-field">
                    <input name="password" id="password" type="password" class="validate">
                    <label for="password">รหัสผ่าน</label>
                </div>

                <button class="btn waves-effect waves-light fullWidth" type="submit">
                    เข้าสู่ระบบ
                </button>
            </form>
        </div>

    </div>
    <br><br>
</div>
@endsection
