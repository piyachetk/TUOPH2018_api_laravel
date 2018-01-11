@extends('layouts.app')

@section("title")
    แผงควบคุม
@endsection

@section("content")
    <div class="container">
        <div class="section">

            @if(session()->has('success'))
                <div class="z-depth-1 card-panel green white-text" style="max-width:800px;margin: 3rem auto auto;">
                    {{ session()->get('success') }}
                </div>
            @endif

            @if(session()->has('error'))
                <div class="z-depth-1 card-panel red white-text" style="max-width:800px;margin: 3rem auto auto;">
                    {{ session()->get('error') }}
                </div>
            @endif

            <div class="z-depth-1 card-panel white" style="max-width:800px;margin: 3rem auto auto;">
                <h5 class="center">Control Panel</h5>
                <a href="/admin/check"
                   class="waves-effect waves-light btn green fullWidth" style="margin-bottom: 8px;">
                    ตรวจสอบรหัสยืนยันการลงทะเบียน
                </a>
                <a href="/admin/edit"
                   class="waves-effect waves-light btn orange fullWidth" style="margin-bottom: 8px;">
                    Dirty Data Patch
                </a>
                <a href="/logout"
                   class="waves-effect waves-light btn red fullWidth">
                    ออกจากระบบ
                </a>
            </div>

        </div>
        <br><br>
    </div>
@endsection
