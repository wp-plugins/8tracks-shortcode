(function () {
	tinymce.create('tinymce.plugins.examplePlugin', {
		init : function (ed, url) {
			ed.addCommand('example', function () {
				ed.windowManager.open({
					file : url + '/8tracks_button.htm',
					width : 570 + parseInt(ed.getLang('example.delta_width', 0)),
					height : 450 + parseInt(ed.getLang('example.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});
			ed.addButton('example', {title : 'example', cmd : 'example', image: url + '/icon.jpg' });
		},
		getInfo : function () {
			return {
				longname : 'example',
				author : 'Jon Martin',
				authorurl : 'http://www.shh-listen.com',
				infourl : 'https://wordpress.org/extend/plugins/8tracks-shortcode/',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});
	tinymce.PluginManager.add('example', tinymce.plugins.examplePlugin);
}());

