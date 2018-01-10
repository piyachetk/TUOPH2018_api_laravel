@extends('layouts.app')

@section("title")
    Dirty Data Patch
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
            <h5 class="center">Dirty Data Patch</h5>

            <form method="POST" action="/admin/edit">
                <div class="input-field">
                    <input name="code" id="code" type="text" class="validate">
                    <label for="code">รหัสยืนยันการลงทะเบียน</label>
                </div>
                <div class="input-field">
                    <input name="field" id="field" type="text" class="validate">
                    <label for="field">Field</label>
                </div>
                <div class="input-field">
                    <input name="value" id="value" type="text" class="validate">
                    <label for="value">Value</label>
                </div>

                <button class="btn waves-effect waves-light fullWidth" type="submit">
                    แก้ไข
                </button>
            </form>
        </div>

    </div>
    <br><br>
</div>
@endsection
