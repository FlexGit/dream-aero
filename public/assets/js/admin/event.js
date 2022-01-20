$(function(){
	// Control Sidebar
	let controlSidebar = localStorage.getItem('control-sidebar');
	if (controlSidebar == 'expanded') {
		$('.control-sidebar').ControlSidebar('show');
	} else {
		$('.control-sidebar').ControlSidebar('collapse');
	}
	$(document).on('collapsed.lte.controlsidebar', '[data-widget="control-sidebar"]', function(e) {
		localStorage.setItem('control-sidebar', 'collapsed');
	});
	$(document).on('expanded.lte.controlsidebar', '[data-widget="control-sidebar"]', function(e) {
		console.log(111);
		localStorage.setItem('control-sidebar', 'expanded');
	});
});
