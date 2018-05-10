<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "ulang://common/meta" [
	<!ENTITY sys-module        'meta'>
	<!ENTITY sys-method-add        'add'>
	<!ENTITY sys-method-edit    'edit'>
	<!ENTITY sys-method-del        'del'>

]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="/result[@method = 'seo_template']/data[@type = 'list' and @action = 'view']" priority="1">
		<script type="text/javascript"><![CDATA[
			var metaDoGenerate = function() {
				for (var id in oTable.selectedList) {
					var h = '<div class="exchange_container">';
							h += '<div id="process-header">' + getLabel('js-meta-generate-help') + '</div>';
							h += '<div><img id="process-bar" src="/images/cms/admin/mac/process.gif" class="progress" /></div>';
							h += '<div class="status">' + getLabel('js-meta-generated') + '<span id="generated_counter">0</span></div>';
							h += '<div id="errors_message" class="status">' + getLabel('js-meta-generate-errors') + '<span id="errors_counter">0</span>' + '</div>';
							h += '<div><a href="#" onclick="$(\'#import_log\').toggle();return false;">' + getLabel('js-meta-toggle-log') + '</a></div>';
							h += '<div id="import_log" style="display:none;"></div>';
						h += '</div>';
						h += '<div class="eip_buttons">';
							h += '<input id="ok_btn" type="button" value="' + getLabel('js-meta-btn_ok') + '" class="ok" style="margin:0;" disabled="disabled" />';
							h += '<input id="repeat_btn" type="button" value="' + getLabel('js-meta-btn_repeat') + '" class="repeat" disabled="disabled" />';
							h += '<input id="stop_btn" type="button" value="' + getLabel('js-meta-btn_stop') + '" class="stop" />';
							h += '<div style="clear: both;"/>';
						h += '</div>';


					openDialog({
						stdButtons: false,
						title      : getLabel('js-meta-generate'),
						text       : h,
						width      : 390,
						OKCallback : function () {

						}
					});

					var i_generated = 0;
					var i_errors = 0;

					var b_canceled = false;

					var reportError = function(msg) {
						$('#errors_message').css('color', 'red');
						i_errors++;
						$('#errors_counter').html(i_errors);
						$('#import_log').append(msg + "<br />");
						$('#process-header').html(msg).css('color', 'red');
						$('#process-bar').css({'visibility' : 'hidden'});
						$('#repeat_btn').one("click", function() { b_canceled = false; processGenerate(); }).removeAttr('disabled');
						$('#ok_btn').one("click", function() { closeDialog(); }).removeAttr('disabled');
						$('#stop_btn').attr('disabled', 'disabled');

						if(window.session) {
							window.session.stopAutoActions();
						}

					}

					var processGenerate = function () {
						$('#process-bar').css({'visibility' : 'visible'});
						$('#process-header').html(getLabel('js-meta-generate-help')).css({'color' : ''});
						$('#repeat_btn').attr('disabled', 'disabled');
						$('#ok_btn').attr('disabled', 'disabled');
						$('#stop_btn').one("click", function() { b_canceled = true; $(this).attr('disabled', 'disabled'); }).removeAttr('disabled');

						if(window.session) {
							window.session.startAutoActions();
						}
						console.log(id);
						$.ajax({
							type: "GET",
							url: "/admin/meta/generate_do/" + id + "/.xml"+"?r=" + Math.random(),
							dataType: "xml",

							success: function(doc){
								$('#process-bar').css({'visibility' : 'hidden'});
								var errors = doc.getElementsByTagName('error');
								if (errors.length) {
									reportError(errors[0].firstChild.nodeValue)
									return;
								}
								// write log
								var log = doc.getElementsByTagName('log');
								for (var i = 0; i < log.length; i++) {
									$('#import_log').append(log[i].firstChild.nodeValue + "<br />");
								}
								// updated counts
								var data_nl = doc.getElementsByTagName('data');
								if (!data_nl.length) {
									reportError(getLabel('js-meta-ajaxerror'));
									return false;
								}
								var data = data_nl[0];
								i_generated += (parseInt(data.getAttribute('generated')) || 0);

								$('#generated_counter').html(i_generated);

								var complete = data.getAttribute('complete') || false;

								if (complete === false) {
									reportError(getLabel('Parse data error. Required attribute complete not found'));
									exit();
								}

								if (complete == 1) {
									console.log(doc);
									$('#process-header').html(getLabel('js-meta-generate-done')).css({'color' : 'green'});
									$('#stop_btn').attr('disabled', 'disabled');
									$('#ok_btn').one("click", function() { closeDialog(); }).removeAttr('disabled');

									if(window.session) {
										window.session.stopAutoActions();
									}
								} else {
									if (b_canceled) {
										$('#repeat_btn').one("click", function() { b_canceled = false; processGenerate(); }).removeAttr('disabled');
										$('#ok_btn').one("click", function() { closeDialog(); }).removeAttr('disabled');
									} else {
										processGenerate();
									}
								}


							},

							error: function(event, XMLHttpRequest, ajaxOptions, thrownError) {
								if(window.session) {
									window.session.stopAutoActions();
								}

								reportError(getLabel('js-meta-ajaxerror'));
							}

						});
					}

					processGenerate();


					break;
				}
			}
		]]></script>

		<div class="imgButtonWrapper">
			<a href="{$lang-prefix}/admin/&sys-module;/add/">
				<xsl:text>&label-seo-add;</xsl:text>
			</a>
			<a href="#" id="doGenerate" onclick = "metaDoGenerate(); return false;" style="background: url(/images/cms/admin/mac/ico_exchange.png) no-repeat 0 0;">
				<xsl:text>&label-seo-generate;</xsl:text>
			</a>
		</div>

        <xsl:call-template name="ui-smc-table">
            <xsl:with-param name="content-type">objects</xsl:with-param>
            <xsl:with-param name="control-params"><xsl:value-of select="/result/@method"/></xsl:with-param>
            <xsl:with-param name="js-add-buttons">
                createAddButton(
                    $('#doGenerate')[0],	oTable, '#', ['*']
                );
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>

</xsl:stylesheet>