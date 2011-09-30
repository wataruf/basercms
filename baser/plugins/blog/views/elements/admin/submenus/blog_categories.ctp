<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログカテゴリ管理メニュー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<div class="side-navi">
	<h2>カテゴリ管理メニュー</h2>
	<ul>
		<li><?php $baser->link('ブログカテゴリ一覧',array('controller'=>'blog_categories','action'=>'index',$blogContent['BlogContent']['id'])) ?></li>
<?php if($newCatAddable): ?>
		<li><?php $baser->link('新規ブログカテゴリを登録',array('controller'=>'blog_categories','action'=>'add',$blogContent['BlogContent']['id'])) ?></li>
<?php endif ?>
	</ul>
</div>
