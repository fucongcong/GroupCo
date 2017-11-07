CREATE TABLE `consumers` (
  `id` int(11) unsigned NOT NULL,
  `service` varchar(255) NOT NULL COMMENT '需要的服务',
  `address` varchar(50) NOT NULL COMMENT '消费者主机',
  `ctime` int(10) unsigned NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消费者';

CREATE TABLE `providers` (
  `id` int(10) unsigned NOT NULL,
  `service` varchar(255) NOT NULL,
  `address` varchar(50) NOT NULL,
  `ctime` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `consumers`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `providers`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `consumers`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `providers`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;