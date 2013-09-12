 $(document).ready(function() { 
 	var openBox=false;
 	var parentOfBox;
 	
		$(".player").click(function() {
			name = "#box_" + $(this).attr( "player" ).replace(/ /, "_");
			if (!openBox) {
				$(name).fadeIn("slow");
				openBox=true;
			} else {
				$(name).fadeOut("slow");;
				openBox=false;				
			}	
   });

		$(".player").hover(function() {
   			$(this).addClass("B"); 	
  		},
				function() {
   			$(this).removeClass("B"); 		
 		});

 });
 
