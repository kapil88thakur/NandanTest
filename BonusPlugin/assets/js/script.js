jQuery(document).ready(function(){
    jQuery('#myModal').css('display','block');

    jQuery('.close').click(function(){
      jQuery('#myModal').css('display','none');
    });
    jQuery(window).click(function(){
        jQuery('#myModal').css('display','none');
  });

  });
