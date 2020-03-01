<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "ulang://common">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="group" mode="settings.modify">
        <div class="panel properties-group">
            <div class="header">
                <span>
                    <xsl:value-of select="@label" />
                </span>
                <div class="l" /><div class="r" />
            </div>
            <div class="content">
                <xsl:apply-templates select="option[@type='boolean']" mode="guides.settings.modify" />
                <xsl:call-template name="std-save-button" />
            </div>
        </div>
    </xsl:template>

    <xsl:template match="option" mode="guides.settings.modify">
        <div class="field" style="width:24%">
            <label class="inline" for="{generate-id()}">
                <span class="label">
                    <input id="{generate-id()}" class="checkbox" type="checkbox" value="1" name="type[{value/id}]">
                        <xsl:if test="value/value = '1'">
                            <xsl:attribute name="checked">checked</xsl:attribute>
                        </xsl:if>
                    </input>
                    <acronym>
                        <xsl:value-of select="value/name"/>
                    </acronym>
                </span>
            </label>
        </div>
    </xsl:template>

</xsl:stylesheet>