// JavaScript Document



jQuery(document).ready(function(e) {
    
	
	
	
	jQuery('.form-item-checkboxes input[type=checkbox]').each(function(index, element) {
        
		var theVal = jQuery(this).next('label').text().toLowerCase();
		jQuery(this).attr('value',theVal);
		
    });
	
	
	jQuery('textarea[data-wpt-id=wpcf-coordonnees-brutes]').after(jQuery('<input type="button" class="update_coord" value="Mise à jour" />'));
	
	

	
});

	jQuery(document).on('click','a.js-wpt-repdelete',function(){
		//alert('Index: '+jQuery(this).parent().parent().index());
		if(jQuery(this).attr('data-wpt-id') == 'wpcf-latitude'){
			var theIndex = jQuery(this).parent().parent().index();
			theIndex = theIndex+1;
			
			var selector = '.js-wpt-repetitive.wpt-repetitive[data-wpt-id=wpcf-longitude] > .js-wpt-field-items.ui-sortable > .wpt-field-item:nth-child('+theIndex+') a.js-wpt-repdelete';
			//alert(jQuery(this).parent().parent().parent().parent().attr('class'));
			jQuery(selector).click();
			
			//alert(jQuery(this).index());
			//jQuery('a.js-wpt-repadd.wpt-repadd[data-wpt-id=wpcf-longitude]').click();	
		}
		
	});
	
		jQuery(document).on('click', 'input.update_coord', function(){
		
		// on détruit les entrées existantes
		jQuery('a.js-wpt-repdelete[data-wpt-id=wpcf-latitude]').click();
		
		var workString = jQuery('textarea[data-wpt-id=wpcf-coordonnees-brutes]').text();
		var myArray = workString.split("\n");
		for(i=0;i<myArray.length;i++)  {
			
			if(i<myArray.length-1)
			{
			jQuery('a.js-wpt-repadd.wpt-repadd[data-wpt-id=wpcf-latitude]').click();
			}
			
			theIndex = i+1;
    		var selectorLng = '.js-wpt-repetitive.wpt-repetitive[data-wpt-id=wpcf-longitude] > .js-wpt-field-items.ui-sortable > .wpt-field-item:nth-child('+theIndex+') input.wpt-form-textfield.form-textfield.textfield';
			var selectorLat = '.js-wpt-repetitive.wpt-repetitive[data-wpt-id=wpcf-latitude] > .js-wpt-field-items.ui-sortable > .wpt-field-item:nth-child('+theIndex+') input.wpt-form-textfield.form-textfield.textfield';
			
			var currentLine = myArray[i];
			var theParts = currentLine.split(";");
			
			jQuery(selectorLat).val(theParts[0]);
			jQuery(selectorLng).val(theParts[1]);
			
			
			
		}
		
		//alert(myArray);
	});
	
	jQuery(document).on('click','a.js-wpt-repadd.wpt-repadd',function(){
	
		if(jQuery(this).attr('data-wpt-id') == 'wpcf-latitude'){
			jQuery('a.js-wpt-repadd.wpt-repadd[data-wpt-id=wpcf-longitude]').click();	
		}
		
	});