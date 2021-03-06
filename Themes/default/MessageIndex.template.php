<?php
/**
 * @name      EosAlpha BBS
 * @copyright 2011 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0pre
 */
function template_main()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	echo '
	<a id="top"></a>
	<h1 class="bigheader">',$context['name'],'</h1>';
	
	if (!empty($options['show_board_desc']) && $context['description'] != '')
		echo '
	<div class="smalltext">', $context['description'], '</div>';

	if (count($context['boards']) && (!empty($options['show_children']) || $context['start'] == 0))
	{
		$collapser = array('id' => $context['current_board'] . '_childboards',
			'title' => $txt['parent_boards'], 'bodyclass' => 'generic_container');
		
		echo '<br>';
		template_create_collapsible_container($collapser);
		echo '<div class="framed_region smallpadding"><ol id="board_', $context['current_board'], '_children" class="commonlist category">';

		$context['alternate'] = 1;
		foreach ($context['boards'] as &$board) {
			if(!$board['ignored'])
				template_boardbit($board);
		}
		echo '
			</ol>
			</div>
			</div>
			';
	}
	if($context['hidden_boards']['hidden_count']) {
		echo '
			<br>
			<div id="show_hidden_boards" class="orange_container norounded gradient_darken_down tinytext"><span class="floatright">',$context['hidden_boards']['setup_notice'],'</span><strong>',sprintf($context['hidden_boards']['notice'], $context['hidden_boards']['hidden_count'], '<a onclick="$(\'div#hidden_boards\').fadeIn();return(false);" href="!#">'),'</strong></div>
			<div class="category" id="hidden_boards" style="display:none;">
				<div class="framed_region cleantop root_cat" id="category_0">
					<ol class="commonlist category">';
		foreach ($context['hidden_boards']['boards'] as &$board) {
			if($board['ignored'])
				template_boardbit($board);
		}
		echo '
					</ol>
				</div>
			</div>
			<div class="cContainer_end"></div>';
	}

	if(!$context['act_as_cat']) {
	
	$normal_buttons = array(
		'new_topic' => array('test' => 'can_post_new', 'text' => 'new_topic', 'image' => 'new_topic.gif', 'lang' => true, 'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0', 'active' => true),
		'post_poll' => array('test' => 'can_post_poll', 'text' => 'new_poll', 'image' => 'new_poll.gif', 'lang' => true, 'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0;poll'),
		'notify' => array('test' => 'can_mark_notify', 'text' => $context['is_marked_notify'] ? 'unnotify' : 'notify', 'image' => ($context['is_marked_notify'] ? 'un' : ''). 'notify.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . ($context['is_marked_notify'] ? $txt['notification_disable_board'] : $txt['notification_enable_board']) . '\');"', 'url' => $scripturl . '?action=notifyboard;sa=' . ($context['is_marked_notify'] ? 'off' : 'on') . ';board=' . $context['current_board'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'markread' => array('text' => 'mark_read_short', 'image' => 'markread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=board;board=' . $context['current_board'] . '.0;' . $context['session_var'] . '=' . $context['session_id']),
	);

	// They can only mark read if they are logged in and it's enabled!
	if (!$context['user']['is_logged'] || !$settings['show_mark_read'])
		unset($normal_buttons['markread']);

	// Allow adding new buttons easily.
	HookAPI::callHook('integrate_messageindex_buttons', array(&$normal_buttons));

	if (!$context['no_topic_listing'])
	{
		echo '
	<div class="pagesection top smallpadding">
		<div class="pagelinks floatleft">', $context['page_index'], '&nbsp;&nbsp;<a class="navPages" href="#bot">' . $txt['go_down'] . '</a></div>
		', template_button_strip($normal_buttons, 'right'), '
	</div>';

		// If Quick Moderation is enabled start the form.
		if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] > 0 && !empty($context['topics']))
			echo '
	<form action="', $scripturl, '?action=quickmod;board=', $context['current_board'], '.', $context['start'], '" method="post" accept-charset="UTF-8" class="clear" name="quickModForm" id="quickModForm">';

		echo '
		<div class="framed_region">
		<table class="topic_table">';

		// Are there actually any topics to show?
		if (!empty($context['topics']))
		{
			echo '
					<thead>
					<tr class="mediumpadding">
					<th scope="col" colspan="2" class="first_th glass cleantop" style="width:8%;">&nbsp;</th>
					<th scope="col" class="lefttext nowrap glass cleantop"><a rel="nofollow" href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=subject', $context['sort_by'] == 'subject' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['subject'], $context['sort_by'] == 'subject' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a> / <a rel="nofollow" href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=starter', $context['sort_by'] == 'starter' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['started_by'], $context['sort_by'] == 'starter' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></th>
					<th scope="col" class="nowrap glass cleantop"><a rel="nofollow" href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=replies', $context['sort_by'] == 'replies' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['replies'], $context['sort_by'] == 'replies' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a> / <a href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=views', $context['sort_by'] == 'views' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['views'], $context['sort_by'] == 'views' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></th>
					<th scope="col" class="centertext nowrap glass cleantop"><a rel="nofollow" href="', $scripturl, '?board=', $context['current_board'], '.', $context['start'], ';sort=last_post', $context['sort_by'] == 'last_post' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['last_post'], $context['sort_by'] == 'last_post' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></th>';

			// Show a "select all" box for quick moderation?
			if (!empty($context['can_quick_mod']))
				echo '
					<th scope="col" class="glass cleantop last_th" style="width:24px;"><input type="checkbox" class="input_check cb_invertall" /></th>';
		}
		// No topics.... just say, "sorry bub".
		else
			echo '
					<thead>
					<tr>
					<th class="red_container"><strong>', $txt['msg_alert_none'], '</strong></th>';

		echo '
				</tr>
			</thead>';
		if (!empty($settings['display_who_viewing']))
		{
			echo '
				<tr class="whos_viewing mediumpadding">
					<td colspan="', !empty($context['can_quick_mod']) ? '6' : '5', '" class="smalltext">';
			if ($settings['display_who_viewing'] == 1)
				echo count($context['view_members']), ' ', count($context['view_members']) === 1 ? $txt['who_member'] : $txt['members'];
			else
				echo empty($context['view_members_list']) ? '0 ' . $txt['members'] : implode(', ', $context['view_members_list']) . ((empty($context['view_num_hidden']) or $context['can_moderate_forum']) ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');
			echo $txt['who_and'], $context['view_num_guests'], ' ', $context['view_num_guests'] == 1 ? $txt['guest'] : $txt['guests'], $txt['who_viewing_board'], '
					</td>
				</tr>';
		}

		// If this person can approve items and we have some awaiting approval tell them.
		if (!empty($context['unapproved_posts_message']))
		{
			echo '
				<tr class="windowbg2">
					<td colspan="', !empty($context['can_quick_mod']) ? '6' : '5', '">
						<span class="alert">!</span> ', $context['unapproved_posts_message'], '
					</td>
				</tr>';
		}

		foreach ($context['topics'] as &$topic)
		{
			template_topicbit($topic);
		}

		if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] && !empty($context['topics']))
		{
			echo '
				<tr>
					<td colspan="6" class="righttext">
						<select class="qaction" name="qaction"', $context['can_move'] ? ' onchange="this.form.moveItTo.disabled = (this.options[this.selectedIndex].value != \'move\');"' : '', '>
							<option value="">--------</option>', $context['can_remove'] ? '
							<option value="remove">' . $txt['quick_mod_remove'] . '</option>' : '', $context['can_lock'] ? '
							<option value="lock">' . $txt['quick_mod_lock'] . '</option>' : '', $context['can_sticky'] ? '
							<option value="sticky">' . $txt['quick_mod_sticky'] . '</option>' : '', $context['can_move'] ? '
							<option value="move">' . $txt['quick_mod_move'] . ': </option>' : '', $context['can_merge'] ? '
							<option value="merge">' . $txt['quick_mod_merge'] . '</option>' : '', $context['can_restore'] ? '
							<option value="restore">' . $txt['quick_mod_restore'] . '</option>' : '', $context['can_approve'] ? '
							<option value="approve">' . $txt['quick_mod_approve'] . '</option>' : '', $context['user']['is_logged'] ? '
							<option value="markread">' . $txt['quick_mod_markread'] . '</option>' : '', '
						</select>';

			// Show a list of boards they can move the topic to.
			if ($context['can_move'])
			{
					echo '
						<select class="qaction" id="moveItTo" name="move_to" disabled="disabled">';

					foreach ($context['move_to_boards'] as $category)
					{
						echo '
							<optgroup label="', $category['name'], '">';
						foreach ($category['boards'] as $board)
								echo '
								<option value="', $board['id'], '"', $board['selected'] ? ' selected="selected"' : '', '>', $board['child_level'] > 0 ? str_repeat('==', $board['child_level'] - 1) . '=&gt;' : '', ' ', $board['name'], '</option>';
						echo '
							</optgroup>';
					}
					echo '
						</select>';
			}

			echo '
						<input type="submit" value="', $txt['quick_mod_go'], '" onclick="return document.forms.quickModForm.qaction.value != \'\' &amp;&amp; confirm(\'', $txt['quickmod_confirm'], '\');" class="button_submit qaction" />
					</td>
				</tr>';
		}

		echo '
			</tbody>
		</table>
	</div>';
	}
	echo '
	<a id="bot"></a>';

		// Finish off the form - again.
		if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] > 0 && !empty($context['topics']))
			echo '
	<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />
	</form>';

		echo '
	<div class="pagesection bottom smallpadding">
		', template_button_strip($normal_buttons, 'right'), '
		<div class="pagelinks floatleft">', $context['page_index'], '&nbsp;&nbsp;<a class="navPages" href="#top">' . $txt['go_up'] . '</a></div>
	</div>';
	}

	theme_linktree();
	
	echo '
	<div class="tborder" id="topic_icons">
		<div class="description">
			<p class="floatright" id="message_index_jump_to">&nbsp;</p>';

	$context['inline_footer_script'] .= '
	if (typeof(window.XMLHttpRequest) != "undefined")
		aJumpTo[aJumpTo.length] = new JumpTo({
		sContainerId: "message_index_jump_to",
		sJumpToTemplate: "<label class=\"smalltext\" for=\"%select_id%\">'.$context['jump_to']['label'].':<" + "/label> %dropdown_list%",
		iCurBoardId: '.$context['current_board'].',
		iCurBoardChildLevel: '.$context['jump_to']['child_level'].',
		sCurBoardName: "'.$context['jump_to']['board_name'].'",
		sBoardChildLevelIndicator: "==",
		sBoardPrefix: "=> ",
		sCatSeparator: "-----------------------------",
		sCatPrefix: "",
		sGoButtonLabel: "'.$txt['quick_mod_go'].'"
	});
	';
	echo '
			<br class="clear" />
		</div>
	</div>';

	// Javascript for inline editing.

	$context['inline_footer_script'] .= '
	// Hide certain bits during topic edit.
	hide_prefixes.push("lockicon", "stickyicon", "pages", "newicon");

	// Use it to detect when we\'ve stopped editing.
	document.onclick = modify_topic_click;

	var mouse_on_div;
	function modify_topic_click()
	{
		if (in_edit_mode == 1 && mouse_on_div == 0)
			modify_topic_save("'.$context['session_id'].'", "'.$context['session_var'].'");
	}

	function modify_topic_keypress(oEvent)
	{
		if (typeof(oEvent.keyCode) != "undefined" && oEvent.keyCode == 13)
		{
			modify_topic_save("'.$context['session_id'].'", "'.$context['session_var'].'");
			if (typeof(oEvent.preventDefault) == "undefined")
				oEvent.returnValue = false;
			else
				oEvent.preventDefault();
		}
	}

	// For templating, shown when an inline edit is made.
	function modify_topic_show_edit(subject)
	{
		// Just template the subject.
		setInnerHTML(cur_subject_div, \'<input type="text" name="subject" value="\' + subject + \'" size="60" style="width: 95%;" maxlength="80" onkeypress="modify_topic_keypress(event)" class="input_text" /><input type="hidden" name="topic" value="\' + cur_topic_id + \'" /><input type="hidden" name="msg" value="\' + cur_msg_id.substr(4) + \'" />\');
	}

	// And the reverse for hiding it.
	function modify_topic_hide_edit(subject)
	{
		// Re-template the subject!
		setInnerHTML(cur_subject_div, \'<a href="'.$scripturl.'?topic=\' + cur_topic_id + \'.0">\' + subject + \'<\' +\'/a>\');
	}
	';
}

function template_ajaxresponse_whoposted()
{
	global $context, $txt;

	echo '<', '?xml version="1.0" encoding="UTF-8"?', '>
<document>
 <response open="default_overlay" width="auto" offset="-150" />
 <content>
 <![CDATA[
';
	echo '
		<div class="title_bar">
			<h1>',$txt['who_posted'],'</h1>
		</div>';
	
	echo '
		<div class="blue_container mediumpadding mediummargin" style="width:200px;">
		<dl class="common">
			<dt class="red_container lefttext"><strong>&nbsp;&nbsp;',$txt['who_member'],'&nbsp;&nbsp;</strong></dt>
			<dd class="red_container righttext"><strong>&nbsp;&nbsp;',$txt['posts'],'&nbsp;&nbsp;</strong></dd>';
			
	foreach($context['posters'] as $poster)	{
		echo '
			<dt class="lefttext">',$poster['real_name'], '</dt>
			<dd class="righttext">',$poster['count'], '</dd>';
	}
	echo '</dl>
	</div>
	]]>
 </content>
</document>';
}
?>