<tr>
  <td class="{$sequence}">
  <p>{section show=$is_read|not}<b>{/section}{"Posted: %1"|i18n('design/standard/collaboration',,array($item.created|l10n(shortdatetime)))}{section show=$is_read|not}</b>{/section}</p>
  </td>
  <td class="{$sequence}">
  <p>{section show=$is_read|not}<b>[new]</b>{/section}</p>
  </td>

  <td class="{$sequence}" rowspan="2" valign="top">
    <p><nobr>{collaboration_participation_view view=text_linked collaboration_participant=$item_link.participant}</nobr></p>
  </td>
</tr>
<tr>
  <td class="{$sequence}" colspan="2">
  {$item.data_text1|wash(xhtml)}
  </td>
</tr>
