# XBoard-TG机器人签到获取随机1m-1G流量
TG流量机器人签到
下载后将telegram放到/www/wwwroot/域名/plugins/目录下即可 

CREATE TABLE `plugin_telegram_lottery_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `telegram_id` VARCHAR(64) NOT NULL,
  `reward_mb` INT NOT NULL,
  `reward_bytes` BIGINT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_date` (`user_id`, `created_at`),
  KEY `idx_tg_date` (`telegram_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

https://telegraph-image-88g.pages.dev/file/AgACAgUAAyEGAATOlTvtAAMKaTHGdQuHxfxbTntlD910T5KhfA0AAkILaxt9pJFVvYquI1Cgw9UBAAMCAAN5AAM2BA.png

按下面做就行：

先点左侧的数据库名：sql_域名

你截图左侧树形里已经有 sql_域名，点一下让它高亮（确保“选中数据库”）。

点顶部菜单的 SQL（就在“数据库”旁边那个）。

会出现一个大输入框，把我给你的 CREATE TABLE 那段 SQL 粘贴进去。

点右下/右侧的 执行 / Go 按钮。

执行成功后，在左侧表列表刷新，应该能看到新表：
plugin_telegram_lottery_logs

✅ 想确认是否成功：选中库后再点 SQL，执行：
