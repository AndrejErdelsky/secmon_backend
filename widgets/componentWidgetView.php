  <?php
  use yii\helpers\Json;

  $options =  Json::decode($component->config);
  printf("<div class='grid-item card %s' id='component_%s'>", $options['width'], $component->id);
        ?>

                <div class="card-content">
                    <div class="card-header">
                        <span class="card-title activator grey-text text-darken-4"><?php  echo $options['name']; ?><i class="material-icons right">more_vert</i></span>
                    </div>
                    <div class="card-body">
                    </div>
                </div>
                <div class="card-reveal">
                    <div class="card-header light-blue accent-4">
                        <span class="card-title white-text"><?php  echo $options['name']; ?> - options<i class="material-icons right">close</i></span>
                    </div>
                    <div class="card-body">
                        <form class="row componentForm"  data-id="<?php  echo $component->id; ?>">
                            <div class="input-field col s12">
                                <label class="active" for="name">Name</label>
                                <input onfocus="this.select();" onmouseup="return false;" id="name<?php  echo $component->id; ?>" type="text" value="<?php  echo $options['name']; ?>">
                            </div>

                            <div class="input-field col s12">
                                <label class="active">Select width</label>
                                <select id="width<?php  echo $component->id; ?>" class="widthSelect" data-id="component_<?php  echo $component->id; ?>">
                                    <option <?= $options['width'] == '' ? ' selected="selected"' : '' ?> value="">25%</option>
                                    <option <?= $options['width'] == 'width2' ? ' selected="selected"' : '' ?> value="width2">50%</option>
                                    <option <?= $options['width'] == 'width3' ? ' selected="selected"' : '' ?> value="width3">75%</option>
                                    <option <?= $options['width'] == 'width4' ? ' selected="selected"' : '' ?> value="width4">100%</option>
                                </select>
                            </div>
                            
                            <div class="input-field col s12 right-align">
                                <button type="button" class="deleteComponentBtn btn waves-effect waves-light red" data-id="<?php  echo $component->id; ?>">
                                    Delete
                                    <i class="material-icons right">delete</i>
                                </button>
                                <button class="btn waves-effect waves-light green" type="submit">
                                    Save
                                    <i class="material-icons right">save</i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>