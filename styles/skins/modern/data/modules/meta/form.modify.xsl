<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "ulang://common">
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:umi="http://www.umi-cms.ru/TR/umi"
                xmlns:php="http://php.net/xsl">


    <xsl:param name="param0" />

    <!-- Шаблон первой группы полей для сценариев импорта и экспорта -->
    <xsl:template match="group[position() = 1 and count(../../basetype) = 0 and ../../../../@module='meta']"
                  mode="form-modify-group-fields">

        <xsl:param name="show-name"><xsl:text>1</xsl:text></xsl:param>
        <xsl:param name="show-type"><xsl:text>1</xsl:text></xsl:param>

        <xsl:if test="$show-name = '1'">
            <xsl:call-template name="std-form-name">
                <xsl:with-param name="value" select="../../@name" />
                <xsl:with-param name="show-tip"><xsl:text>0</xsl:text></xsl:with-param>
            </xsl:call-template>
        </xsl:if>

        <xsl:choose>
            <xsl:when test="$show-type = '1'">
                <xsl:call-template name="std-form-data-type">
                    <xsl:with-param name="value" select="../../@type-id" />
                </xsl:call-template>
            </xsl:when>
            <xsl:otherwise>
                <input type="hidden" name="type-id" value="{../../@type-id}" />
            </xsl:otherwise>
        </xsl:choose>

        <xsl:apply-templates select="field" mode="form-modify" />
        <script>
            jQuery(document).ready(function(){
                $('.selectize').selectize({
                    plugins: ['remove_button'],
                    allowEmptyOption: true,
                    create: false
                });
            });
        </script>
    </xsl:template>

    <xsl:template match="field[@name = 'hierarchy_type_id']" priority="1" mode="form-modify">
        <div class="col-md-6 clearfix" id="{generate-id()}">
            <div class="title-edit">
                <span class="label">
                    <acronym title="{@tip}">
                        <xsl:apply-templates select="." mode="sys-tips" />
                        <xsl:value-of select="@title" />
                    </acronym>
                </span>
                <span>
                </span>
            </div>
            <div class="layout-row-icon">
                <div class="layout-col-control">
                    <select class="selectize" autocomplete="off" name="{@input_name}" id="relationSelect{generate-id()}">
                        <option value="0">не выбрано</option>
                        <xsl:variable name="value" select="." />
                        <xsl:for-each select="document('udata://system/hierarchyTypesList')/udata/items/item">
                            <option value="{@id}">
                                <xsl:if test="@id = $value">
                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                </xsl:if>
                                <xsl:value-of select="." />
                            </option>
                        </xsl:for-each>
                    </select>
                </div>
            </div>
        </div>
    </xsl:template>

    <xsl:template match="field[@name = 'object_type_id']" priority="1" mode="form-modify">
        <div class="col-md-6 clearfix" id="{generate-id()}">
            <div class="title-edit">
                <span class="label">
                    <acronym title="{@tip}">
                        <xsl:apply-templates select="." mode="sys-tips" />
                        <xsl:value-of select="@title" />
                    </acronym>
                </span>
                <span>
                </span>
            </div>
            <div class="layout-row-icon">
                <div class="layout-col-control">
                    <select class="selectize" autocomplete="off" name="{@input_name}" id="relationSelect{generate-id()}">
                        <option value="0">не выбрано</option>
                        <xsl:variable name="value" select="." />
                        <xsl:for-each select="document('udata://meta/objectsTypesList/3/')/udata/items/item">
                            <option value="{@id}">
                                <xsl:if test="@id = $value">
                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                </xsl:if>
                                <xsl:value-of select="." />
                            </option>
                        </xsl:for-each>
                    </select>
                </div>
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>