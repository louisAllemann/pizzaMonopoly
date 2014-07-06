jQuery(document).ready(function($){
	/******************************
	*	[widget type has changed, show relevant option]
	******************************/
	$(document).on('change', '.wppizza-select', function(e){
		self=$(this);
		self.closest('div').find('.wppizza-selected>p').hide();
		self.closest('div').find('.wppizza-selected>.wppizza-selected-'+self.val()+'').fadeIn();
	});
})