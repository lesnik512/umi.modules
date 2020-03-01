<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "ulang://common">

<xsl:stylesheet
		version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns:umi="http://www.umi-cms.ru/TR/umi"
		xmlns:php="http://php.net/xsl">

	<!-- Шаблон вывода поля "Домен" -->
	<xsl:template match="field[@name = 'domain_id' and @type = 'int']" mode="form-modify" priority="1">
		<xsl:param name="selected.id" select="."/>
		<div class="col-md-6 default-empty-validation">
			<div class="title-edit">
				<acronym title="{@tip}">
					<xsl:apply-templates select="." mode="sys-tips" />
					<xsl:value-of select="@title" />
				</acronym>
				<xsl:apply-templates select="." mode="required_text" />
			</div>
			<div class="layout-row-icon">
				<div class="layout-col-control selectize-container">
					<select class="default newselect required" autocomplete="off" name="{@input_name}">
						<xsl:if test="$selected.id">
							<option value="{$selected.id}" selected="selected">
								<xsl:value-of select="$domains-list/domain[@id = $selected.id]/@host" />
							</option>
						</xsl:if>
						<xsl:apply-templates select="$domains-list" mode="domain_id">
							<xsl:with-param name="selected.id" select="$selected.id" />
						</xsl:apply-templates>
					</select>
				</div>
			</div>
		</div>
	</xsl:template>

	<xsl:template match="domains/domain" mode="domain_id">
		<xsl:param name="selected.id"/>
		<xsl:if test="$selected.id != @id">
			<option value="{@id}">
				<xsl:value-of select="@host" />
			</option>
		</xsl:if>
	</xsl:template>

	<!-- Шаблон вывода поля "Язык" -->
	<xsl:template match="field[@name = 'lang_id' and @type = 'int']" mode="form-modify">
		<xsl:param name="selected.id" select="."/>
		<div class="col-md-6 default-empty-validation">
			<div class="title-edit">
				<acronym title="{@tip}">
					<xsl:apply-templates select="." mode="sys-tips" />
					<xsl:value-of select="@title" />
				</acronym>
				<xsl:apply-templates select="." mode="required_text" />
			</div>
			<div class="layout-row-icon">
				<div class="layout-col-control selectize-container">
					<select class="default newselect required" autocomplete="off" name="{@input_name}">
						<xsl:if test="$selected.id">
							<option value="{$selected.id}" selected="selected">
								<xsl:value-of select="$site-langs/items/item[@id = $selected.id]" />
							</option>
						</xsl:if>
						<xsl:apply-templates select="$site-langs" mode="lang_id">
							<xsl:with-param name="selected.id" select="$selected.id" />
						</xsl:apply-templates>
					</select>
				</div>
			</div>
		</div>
	</xsl:template>

	<xsl:template match="udata[@module = 'system' and @method = 'getLangsList']" mode="lang_id">
		<xsl:param name="selected.id"/>
		<xsl:apply-templates select="items/item" mode="lang_id">
			<xsl:with-param name="selected.id" select="$selected.id" />
		</xsl:apply-templates>
	</xsl:template>

	<xsl:template match="item" mode="lang_id">
		<xsl:param name="selected.id"/>
		<xsl:if test="$selected.id != @id">
			<option value="{@id}">
				<xsl:value-of select="." />
			</option>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
