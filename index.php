<?php
include_once(__DIR__.'/classes/voice.php');
$voice = new voice();
$voice->run();
?><!DOCTYPE html>
<meta charset="utf-8">
<title>voice</title>
<style>@import url(style.css);</style>
<form method="post">
  <input name="voice" type="text" x-webkit-speech>
</form>
<div class="input"></div>
<div class="output"></div>
<iframe></iframe>
<script src="http://www.google.com/jsapi"></script>
<script src="script.js"></script>