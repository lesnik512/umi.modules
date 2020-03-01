<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "ulang://common/banners" [
	<!ENTITY sys-module        'guides'>
	<!ENTITY sys-method-add        'add'>
]>


<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xlink="http://www.w3.org/TR/xlink">
	<xsl:param name="p">0</xsl:param>
	<xsl:param name="param0"/>

	<xsl:template match="data[@type = 'list' and @action = 'view']">
        <div class="panel">
            <div class="tabs">
                <xsl:for-each select="document(concat('udata://guides/getTypesList/',$param0))/udata/type">
                    <a class="header first" href="/admin/guides/lists/{@id}/">
                        <xsl:attribute name="class">
                            <xsl:text>header</xsl:text>
                            <xsl:if test="@active"> act</xsl:if>
                            <xsl:choose>
                                <xsl:when test="position() = 1"> first</xsl:when>
                                <xsl:when test="position() = last()"> last</xsl:when>
                                <xsl:when test="following-sibling::type[@active]"> prev</xsl:when>
                                <xsl:when test="preceding-sibling::type[@active]"> next</xsl:when>
                            </xsl:choose>
                        </xsl:attribute>
                        <span class="c"><xsl:value-of select="@title"/></span>
                        <span class="l"></span>
                        <span class="r"></span>
                    </a>
                </xsl:for-each>
            </div>
            <div class="content">
                <xsl:variable name="type" select="document(concat('udata://guides/getTypesList/',$param0))/udata/type[@active]"/>
                <div class="imgButtonWrapper" xmlns:umi="http://www.umi-cms.ru/TR/umi">
                    <a href="{$lang-prefix}/admin/&sys-module;/&sys-method-add;/{$type/@id}" class="type_select">
                        <xsl:text>&label-add-list; "</xsl:text><xsl:value-of select="$type/@title"/><xsl:text>"</xsl:text>
                    </a>
                    <a href="{$lang-prefix}/admin/data/type_edit/{$type/@id}" class="type_select" style="background: url(/images/cms/admin/mac/ico_edit.gif) no-repeat 0 0;">
                        <xsl:text>&label-type-edit; "</xsl:text><xsl:value-of select="$type/@title"/><xsl:text>"</xsl:text>
                    </a>
                </div>
                <xsl:call-template name="ui-smc-table">
                    <xsl:with-param name="control-type-id"><xsl:value-of select="$param0"/></xsl:with-param>
                    <xsl:with-param name="content-type">objects</xsl:with-param>
                    <xsl:with-param name="control-params"><xsl:value-of select="$type/@id"/></xsl:with-param>
                    <!--<xsl:with-param name="enable-objects-activity">1</xsl:with-param>-->
                </xsl:call-template>
            </div>
        </div>
	</xsl:template>
</xsl:stylesheet>