<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "ulang://common/data" [
        <!ENTITY sys-module        'guides'>
        <!ENTITY sys-method-add        'add'>
]>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="param0"/>

	<xsl:template match="data[@type = 'list' and @action = 'view']">
        <xsl:variable name="type" select="document(concat('udata://guides/getTypesList/',$param0))/udata/type[@active]"/>
        <div class="tabs module">
            <xsl:for-each select="document(concat('udata://guides/getTypesList/',$param0))/udata/type">
                <div class="section selected">
                    <xsl:attribute name="class">
                        <xsl:text>section</xsl:text>
                        <xsl:if test="@active"> selected</xsl:if>
                    </xsl:attribute>
                    <a href="/admin/guides/lists/{@id}/">
                        <xsl:value-of select="@title"/>
                    </a>
                </div>
            </xsl:for-each>
        </div>
		<div class="tabs-content module">
		<div class="section selected">
			<div class="location">
				<div class="imgButtonWrapper loc-left">
                    <a class="btn color-blue" href="{$lang-prefix}/admin/&sys-module;/&sys-method-add;/{$type/@id}/">
                        <xsl:text>&label-add-list;</xsl:text>
                    </a>
                    <a class="btn color-blue" href="{$lang-prefix}/admin/data/type_edit/{$type/@id}/">
                        <xsl:text>&label-type-edit;</xsl:text>
                    </a>
				</div>
				<a class="btn-action loc-right infoblock-show"><i class="small-ico i-info"></i><xsl:text>&help;</xsl:text></a>
			</div>

			<div class="layout">
				<div class="column">
					<xsl:call-template name="ui-smc-table">
                        <xsl:with-param name="control-type-id"><xsl:value-of select="$param0"/></xsl:with-param>
                        <xsl:with-param name="content-type">objects</xsl:with-param>
                        <xsl:with-param name="control-params"><xsl:value-of select="$type/@id"/></xsl:with-param>
					</xsl:call-template>
				</div>
				<div class="column">
					<div  class="infoblock">
						<h3>
							<xsl:text>&label-quick-help;</xsl:text>
						</h3>
						<div class="content" title="{$context-manul-url}">
						</div>
						<div class="infoblock-hide"></div>
					</div>
				</div>

			</div>
		</div>
		</div>
	</xsl:template>

</xsl:stylesheet>