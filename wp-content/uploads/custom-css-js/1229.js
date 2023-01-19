<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
 


jQuery(document).ready(function(){
jQuery(".item input.select-dropdownmaterial").click(function(){
    jQuery(".tlol").addClass("intro");
  });
});
jQuery(document).on('click', function(e) {
    if ( e.target.class != 'intro' ) {
     jQuery('.tlol').removeClass('intro');
    }
});

jQuery(document).ready(function(){
jQuery(".popat input.select-dropdownmaterial").click(function(){
    jQuery(".lol").addClass("intro");
	
  });
});
jQuery(document).on('click', function(e) {
    if ( e.target.class != 'intro' ) {
     jQuery('.lol').removeClass('intro');
    }
});</script>
<!-- end Simple Custom CSS and JS -->
