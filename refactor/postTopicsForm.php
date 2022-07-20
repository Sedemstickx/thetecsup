<p>
    <label for="inputText">Add up to 5 topics for the post. Use <kbd>,</kbd> to enter new tags - optional</label>
      <div class="div-input"><span id="tagContainer" style="cursor:pointer;display:inline;"><?php echo topic::topic_tags($topic_result); ?></span>
      <input id="inputText" list="topics" type="text" class="tag-input" minlength="1" placeholder="e.g Audio,PC Gaming" maxlength="30">
      <datalist id="topics">
      <?php echo topic::refactored_find_all(); ?>
      </datalist>
      <input id="topic" type="hidden" name="topic" minlength="1" value="<?php echo $topic_result; ?>" maxlength="80">
      </div>
</p>