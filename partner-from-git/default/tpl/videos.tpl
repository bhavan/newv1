<table cellspacing="5" width="100%">
  <thead>
    <tr>
      <th colspan="2"  align="left"><h2><?php echo $var->site_name; ?> Videos</h2></th>
    </tr>
  </thead>
  <tbody>
    <?php
      $two = 0;
      $first = true;
      echo '<tr>';
      foreach($param as $v) {
        $two++;
        if(($two%2) == 1 && !$first) {
          echo '<tr>';
        }
    ?>
      <td align="center" style="padding-right:30px;">
        <?php echo $v['videocode']; ?><br />
        <span style="font-size:14px;"><?php echo $v['title']; ?></span>
      </td>
    <?php
        if(($two%2) == 0 && $two != 0) {
          echo '</tr>';
        }
        $first = false;
      }
      if($two%2 == 1)
        echo '<td>&nbsp;</td></tr>';
    ?>
    <tr>
      <td colspan="2"><br /><br /><?php echo _gen_page(); ?></td>
    </tr>
  </tbody>
</table>

