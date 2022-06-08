<?php 
$currentAry[str_replace(['.php','.html','/'],['','',''],$_SERVER['SCRIPT_NAME'])] = 'current';
?> 
    <aside id="navi" >
      <div class="headLogo"><?php echo GROUP_NAME ?>会員管理システム<span>ver.<?php echo VER ?></span></div>
      <ul>
        <li class="<?php echo $currentAry['top'] ?>" ><a href="/top.php" ><i class="fas fa-home"></i>トップ（最新着歴）</a></li>
        <li class="<?php echo $currentAry['history'] ?>" ><a href="/history.php" ><i class="fas fa-hotel"></i>着信履歴検索</a></li>
        <li class="<?php echo $currentAry['usage'] ?>" ><a href="/usage.php" ><i class="fas fa-hotel"></i>利用履歴検索</a></li>
        <li class="<?php echo $currentAry['customer'] ?>" ><a href="/customer.php" ><i class="fas fa-user"></i>会員情報検索</a></li>
        <li class="<?php echo $currentAry['import'] ?>" ><a href="/import.php" ><i class="fas fa-user"></i>CSVインポート</a></li>
        <li class="<?php echo $currentAry['config'] ?>" ><a href="/config.php" ><i class="fas fa-user"></i>店舗設定</a></li>
      </ul>
<!--
      <ul>
        <li class="<?php echo $currentAry['staffMaster'] ?>" ><a href="/config.html" ><i class="fas fa-cog"></i>基本設定</a></li>
      </ul>
-->
      <a href="/logout.php" title="ログアウト" id="logoutBtn"><img src="/img/logout.png"><span>ログアウト</span></a>
    </aside>


