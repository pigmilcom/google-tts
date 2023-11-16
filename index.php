<!DOCTYPE html>
<html>
  <head>
    <title>Google Text To Speech</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="normalize.css">

    <style>
      html {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
      }
      body { 
        max-width: 1024px;
        overflow-x: hidden;
        position: relative;
        margin: auto;
        padding: 20px
      }
      form {
        display: flex;
        flex-direction: column;
        grid-gap: 5px
      }
      audio {
        width: 100%;
        margin-top: 40px;
        margin-bottom: 40px;
      }
    </style>
  </head>
  <body>
 
  <?php 
    if(isset($_GET['play'])): ?> 
        <audio controls> 
        <source src="demo.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
        </audio>
  <?php endif; ?>

  <form method="post" action="voice-request.php">
      <label>Voice</label>
      <select name="voice"><?php
        $all = json_decode(file_get_contents("voices-list.json"), 1);
        foreach ($all as $v) {
          printf("<option value='%s@%s'>%s (%s)</option>",
            $v["code"], $v["name"],
            $v["name"], $v["gender"]
          );
        }
      ?></select>

      <label>Text</label>
      <textarea name="txt" required></textarea>

      <label>Rate</label>
      <input type="range" min="0.5" max="4.0" value="1" step="0.5" name="rate" oninput="this.nextElementSibling.value = this.value">
      <output class="hint">1</output>

      <label>Pitch</label>
      <input type="range" min="-20" max="20" value="0" step="1" name="pitch" oninput="this.nextElementSibling.value = this.value">
      <output class="hint">0</output>

      <label>Gain</label>
      <input type="range" min="-15" max="15" value="5" step="1" name="gain" oninput="this.nextElementSibling.value = this.value">
      <output class="hint">5</output>

      <input type="submit" value="Send">
    </form>


  </body>
</html>
