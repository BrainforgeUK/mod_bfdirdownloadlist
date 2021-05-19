<?php
/**
 * @package      Joomla.Site
 * @subpackage   mod_bfdirdownloadlist
 * @copyright    Copyright 2021 Jonathan Brain. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
*/

use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

/**
 * @var stdClass    $filelist
 * @var string      $moduleclass_sfx
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

?>
<div class="bfdirdownloadlist<?php echo $moduleclass_sfx ?>">
    <div class="bfdirdownloadlist-list">
        <?php
        foreach($filelist as $file)
        {
            if ($file->id === null)
            {
                continue;
            }
            ?>
            <div class="bfdirdownloadlist-item"
                 style="display:inline-block;width:10em;margin:1em;">
                <a href="<?php echo $file->href; ?>"
                   class="btn <?php echo $file->exists ? 'btn-primary' : 'btn-danger'; ?>"
                   style="width:100%;"
                >
                    <?php echo $file->title; ?>
                </a>
            </div>
            <?php
        }
        ?>
    </div>
</div>
