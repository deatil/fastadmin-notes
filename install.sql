DROP TABLE IF EXISTS `__PREFIX__notes_note`;
CREATE TABLE IF NOT EXISTS `__PREFIX__notes_note` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `content` text COMMENT '内容',
  `tags` text COMMENT '标签',
  `is_top` tinyint(1) NULL DEFAULT '0' COMMENT '1-置顶',
  `status` tinyint(1) NULL DEFAULT '1' COMMENT '状态',
  `edit_time` int(10) DEFAULT '0' COMMENT '编辑时间',
  `edit_ip` varchar(50) DEFAULT '',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='笔记记录';

DROP TABLE IF EXISTS `__PREFIX__notes_tag`;
CREATE TABLE IF NOT EXISTS `__PREFIX__notes_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(150) NOT NULL DEFAULT '' COMMENT '标签名称',
  `keywords` varchar(255) DEFAULT NULL COMMENT '关键字',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `nums` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NULL DEFAULT '1' COMMENT '状态',
  `edit_time` int(10) DEFAULT '0' COMMENT '编辑时间',
  `edit_ip` varchar(50) DEFAULT '',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `nums` (`nums`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='笔记标签';

DROP TABLE IF EXISTS `__PREFIX__notes_note_tag`;
CREATE TABLE IF NOT EXISTS `__PREFIX__notes_note_tag` (
  `note_id` int(10) NOT NULL COMMENT '笔记ID',
  `tag_id` int(10) NOT NULL COMMENT '标签ID',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) DEFAULT '',
  KEY `note_id` (`note_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='笔记标签关联';

