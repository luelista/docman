
-- Exportiere Struktur von Tabelle mwdoc.documents
CREATE TABLE IF NOT EXISTS `documents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `doc_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
  `doc_date` date DEFAULT NULL,
  `doc_inummer` int(11) DEFAULT NULL,
  `doc_mandant` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `doc_inbound` tinyint(4) NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `import_filename` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `import_source` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `tags` text COLLATE utf8_unicode_ci NOT NULL,
  `page_count` int(11) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_hash` varchar(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`,`description`,`tags`)
) ENGINE=MyISAM AUTO_INCREMENT=300 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Exportiere Struktur von Tabelle mwdoc.document_tags
CREATE TABLE IF NOT EXISTS `document_tags` (
  `document_id` int(11) NOT NULL,
  `tag` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  KEY `tag` (`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

