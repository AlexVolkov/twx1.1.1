CREATE TABLE IF NOT EXISTS `%KEY%_accounts` (
 `id` int(8) NOT NULL auto_increment,
  `pair` varchar(255) NOT NULL,
  `error` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pair` (`pair`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

INSERT INTO `%KEY%_accounts` (`id`, `pair`, `error`) VALUES
(1, 'mccorrisf:463595k3h9', '');

CREATE TABLE IF NOT EXISTS `%KEY%_config` (
  `id` int(2) NOT NULL auto_increment,
  `opt_name` text NOT NULL,
  `opt_value` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

INSERT INTO `%KEY%_config` (`id`, `opt_name`, `opt_value`) VALUES
(1, 'use_shortener', 'on'),
(2, 'use_proxy', 'on'),
(3, 'refresh_task_table_intval', '10'),
(4, 'launch_after_add', 'off'),
(5, 'use_proxy_with_errors', 'off'),
(6, 'use_accs_with_errors', 'off');


CREATE TABLE IF NOT EXISTS `%KEY%_tasks` (
  `id` int(2) NOT NULL auto_increment,
  `task_name` text NOT NULL,
  `source` text NOT NULL,
  `used_accounts` int(8) NOT NULL,
  `ordering` text NOT NULL,
  `progress` int(2) NOT NULL,
  `content` text character set ucs2 NOT NULL,
  `status` text NOT NULL,
  `shortener` text NOT NULL,
  `cronIntval` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;


INSERT INTO `%KEY%_tasks` (`id`, `source`, `used_accounts`, `ordering`, `progress`, `content`, `status`, `shortener`, `cronIntval`) VALUES
(2, 'tweets', 100, 'random', 0, 'test twit\n\n', 'stop', 'any', ''),
(3, 'feeds', 100, 'order', 0, 'http://news.google.com/news?pz=1&cf=all&ned=us&hl=en&topic=n&output=rss\r\n', 'stop', 'any', ''),
(4, 'follow', 100, 'order', 0, 'somenik', 'stop', 'none', ''),
(5, 'retweet', 500, 'random', 0, 'http://twitter.com/someacc/status/14969909547678552064', 'stop', 'any', '');
