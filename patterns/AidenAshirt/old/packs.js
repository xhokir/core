(function ($) {
  $(document).ready(function () {

    // identify the option pack image selects
    var fitpack = 'input.fit-option-pack-radio';
    var stylepack = 'input.style-option-pack-radio';

    // Set default options on load
    mmpSetFitOptions($(fitpack+':checked'));
    mmpSetStyleOptions($(stylepack+':checked'));
    
    // Updated options on change
    $(fitpack).change(function(){ mmpSetFitOptions($(this)); });
    $(stylepack).change(function(){ mmpSetStyleOptions($(this)); });

    // Hide detailed options on load
    $('.set-by-fit-option-pack').hide();
    $('.set-by-style-option-pack').hide();
    
    // Show/Hide buttons
    $('#toggle-fit-options').click(function(){ $('.set-by-fit-option-pack').toggle(); });
    $('#toggle-style-options').click(function(){ $('.set-by-style-option-pack').toggle(); });

    /* This handles the fit option pack
     * Takes the object of the select as input */
    function mmpSetFitOptions(input) {
      // Hightlight selected choice in image select
      $('img.selected-fit-pack').removeClass('selected-fit-pack');
      var label = $("label[for='"+input.attr('id')+"'] > img");
      label.addClass('selected-fit-pack');

      // These are the options we need to set
      this.fit_fields = [
        'stretch-factor',
        'armhole-drop'
      ];

      // Option pack presets
      this.skinny_fit =  [  80, 0];
      this.slim_fit =    [  90, 1];
      this.regular_fit = [ 100, 2];
      this.casual_fit =  [ 105, 4];
      this.loose_fit =   [ 115, 6];
      
      // Get to work
      var index;
      var a = this['fit_fields'];
      var value = getLabelFromValue(input.val())
      var v = this[value+'_fit'];
      console.log('==================================================');
      console.log('Fit option pack '+value);
      console.log('==================================================');
      for (index = 0; index < a.length; ++index) {
        $('#edit-configure-submitted-'+a[index]).val(v[index]);
        var thediv = $('#edit-configure-submitted-'+a[index]).parent();
        /* If this input has an input-group div, select the grandparent */
        if($(thediv).hasClass('input-group')) var thediv = $(thediv).parent();
        thediv.addClass('set-by-option-pack set-by-fit-option-pack');
        thediv.attr('data-packname','fit option pack');
        console.log('Setting '+a[index]+ ' to ' + v[index]);
      };
    }

    /* This handles the fit option pack
     * Takes the object of the select as input */
    function mmpSetStyleOptions(input) {
      // Hightlight selected choice in image select
      $('img.selected-style-pack').removeClass('selected-style-pack');
      var label = $("label[for='"+input.attr('id')+"'] > img");
      label.addClass('selected-style-pack');

      // These are the options we need to set
      this.style_fields = [
        'neckline-bend',
        'backline-bend'
      ];

      // Option pack presets
      this.standard_style_fields =  [100,  30];
      this.rounded_style_fields =   [0  ,   0];
      this.racerback_style_fields = [100, 100];
      
      // Get to work
      var index;
      var a = this['style_fields'];
      var value = getLabelFromValue(input.val())
      var v = this[value+'_style_fields'];
      console.log('==================================================');
      console.log('Style option pack '+value);
      console.log('==================================================');
      for (index = 0; index < a.length; ++index) {
        $('#edit-configure-submitted-'+a[index]).val(v[index]);
        var thediv = $('#edit-configure-submitted-'+a[index]).parent();
        /* If this input has an input-group div, select the grandparent */
        if($(thediv).hasClass('input-group')) var thediv = $(thediv).parent();
        thediv.addClass('set-by-option-pack set-by-style-option-pack');
        thediv.attr('data-packname','style option pack');
        console.log('Setting '+a[index]+ '  to ' + v[index]);
      };
    }

    // Helper since Drupal image select module sets value to fid, rather than something useful
    function getLabelFromValue(value) {
      console.log('val = '+value);
      return $('p.webform-select-image-legend-'+value).html().toLowerCase();
    }

  });
}(jQuery));


