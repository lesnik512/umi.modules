<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "ulang://common">

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:param name="domain_id" />

    <xsl:template match="/result[@module = 'seo' and @method = 'robots']/data[@type = 'settings' and @action = 'modify']">
        <xsl:variable name="domains-list" select="document('udata://core/getDomainsList')/udata/domains/domain" />

        <div class="location">
            <div class="save_size"></div>
            <a class="btn-action loc-right infoblock-show"><i class="small-ico i-info"></i><xsl:text>&help;</xsl:text></a>
        </div>
        <div class="layout">
            <div class="column">
                <div id="webo_in">
                    <div class="panel-settings">
                        <div class="title">
                            <h3>
                                <xsl:text>&label-site-robots;</xsl:text>
                            </h3>
                        </div>
                        <div class="content">
                            <form action="" method="get">
                                <div class="field">
                                    <label for="host" style="position:relative; display: block;  width: 100%;">
                                        <span class="label">
                                            <acronym>
                                                &label-site-address;
                                            </acronym>
                                        </span>
                                    </label>
                                    <select autocomplete="off" class="default newselect" name="domain_id" id="domain-selector">
                                        <option selected="selected"></option>
                                        <xsl:apply-templates select="$domains-list" mode="domain-selector"/>
                                    </select>
                                </div>
                                <div class="buttons" style="padding-top:5px;">
                                    <div class="button">
                                        <input type="submit" value="&label-robots-retrieve;" class="btn color-blue btn-small"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <form method="post" action="do/" enctype="multipart/form-data">
                        <input type="hidden" name="domain_id" value="{$domain_id}" />
                        <xsl:apply-templates select="group[@name = 'robots']" mode="settings.modify" />
                        <div class="buttons" style="padding-top:5px;">
                            <div class="button">
                                <input type="submit" value="&label-save;" class="btn color-blue btn-small" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="column">
                <div class="infoblock">
                    <h3><xsl:text>&label-quick-help;</xsl:text></h3>
                    <div class="content" title="{$context-manul-url}">
                    </div>
                    <div class="infoblock-hide"></div>
                </div>
            </div>
        </div>
    </xsl:template>

    <xsl:template match="domain" mode="domain-selector">
        <option value="{@id}">
            <xsl:if test="@id = $domain_id"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
            <xsl:value-of select="@host"/>
        </option>
    </xsl:template>

    <xsl:template match="option[@type = 'text']" mode="settings.modify-option">
        <textarea name="{@name}" id="{@name}">
            <xsl:value-of select="value" disable-output-escaping="yes" />
        </textarea>
    </xsl:template>

</xsl:stylesheet>