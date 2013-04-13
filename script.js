google.load('jquery', '1');
google.setOnLoadCallback(function() {
  $(document).ready(function() {    
    window.talk = function(str) {
      if (str.indexOf('|') != -1) {
        str = str.split('|')[0];
      }
      $('div.output').fadeOut('fast', function() { $(this).html(''); $(this).text(str); $(this).fadeIn('fast'); });
      $('iframe').attr('src', 'http://translate.google.com/translate_tts?tl=de&q='+escape(str));
    };
    window.weather = function(pos, v) {
      v = v+' in ,,,'+Math.round(pos.coords.latitude*1000000, 0)+','+Math.round(pos.coords.longitude*1000000, 0);
      setTimeout(function() {
        console.info('[Q] '+v);
        $.post(location.href, {v:v}, function(response) {
          console.info('[A] '+response);
          talk(response);
        });
      }, 500);
    };
    window.speech = function(v) {
      console.info('[Q] '+v);
      $('div.input').fadeOut('fast', function() { $(this).html(''); $(this).text(v); $(this).fadeIn('fast'); });
      $.post(location.href, {v:v}, function(response) {
        if (response.substring(0, 9) == 'redirect:') {
          talk('Ich habe die Aktion für dich durchgeführt.');
          window.open(response.substring(9), '_blank');
        } else if (response.substring(0, 9) == 'location:') {
          navigator.geolocation.getCurrentPosition(
            function(pos) { weather(pos, response.substring(9)); },
            function(error) { talk('Ich weiß nicht wo du gerade bist.'); }
          );
        } else {
          console.info('[A] '+response);
          talk(response);
        }
      });
    };
    $('form input[name="voice"]').attr('onwebkitspeechchange', 'speech(this.value);');
  });
});