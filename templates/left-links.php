<?php 
function getDynamicLinks($dbh,$parentId = NULL){
      $menu = '';
      $status = 1;
      if(is_null($parentId)){
        $getDynamicLink = "SELECT * from alumni_dynamic_left_links WHERE status =:status AND parent_id IS :dynamic_id ORDER BY priority ASC";
        $query = $dbh->prepare($getDynamicLink);
        $query->bindParam(':status',$status);
        $query->bindParam(':dynamic_id',$parentId);
      }else{
        $getDynamicLink = "SELECT * from alumni_dynamic_left_links WHERE status =:status AND parent_id = :dynamic_id ORDER BY priority ASC";
        $query = $dbh->prepare($getDynamicLink);
        $query->bindParam(':status',$status);
        $query->bindParam(':dynamic_id',$parentId);
      }
      $query->execute();
      $result = $query->fetchAll();
      foreach($result as $rowData){
        if($rowData['link_url'] != 'javascript:void(0)'){
          $menu .= '<li><a href="'.$rowData['link_url'].'"  title="'.$rowData['link_name'].'">
            '.$rowData['icon'].'
            <span>'.$rowData['link_name'].'</span>
            </a>';
        }else{
          $menu .= '<li><a href="'.$rowData['link_url'].'" class="menu-toggle" title="'.$rowData['link_name'].'">
            '.$rowData['icon'].'
            <span>'.$rowData['link_name'].'</span>
            </a>';
        }
        $menu .= '<ul class="ml-menu">'.getDynamicLinks($dbh, $rowData['dynamic_id']).'</ul>';
        $menu .= '</li>';
      }
      return $menu;
    }
?>
<section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <!-- User Info -->
            <?php
                $dbobj = new GetCommomOperation();
                $loginSessionId = $_SESSION['SESS_ADMIN_ID'];
                $getLoggedData = $dbobj->selectData($dbh, TBL_PRIFIX.'user_login_details', array('registration_id','profile_pic','profession','present_address','permanent_address','pincode','blood_group','skills_id','merital_status','dob','course_id','city','state','country','fname','lname','year_of_graduation','concat_ws(" ",fname,lname) as fullName','email_id','mobile_number','gender','user_type','terms_and_condition','year_of_graduation'), 'user_id = ?', array($loginSessionId));
                if($getLoggedData['total'] > 0){
                    $getLoggedData = $getLoggedData['values'][0];
                }
            ?>
            <div class="user-info">
                <div class="image">
                    <a href="profile"><img src="<?php echo (!empty($getLoggedData['profile_pic']) && $getLoggedData['profile_pic'] != NULL ? ALUM_WS_UPLOADS.ALUM_PROFILE.$getLoggedData['profile_pic'] : ALUM_WS_IMAGES.'user.png');?>" width="48" height="48" alt="User" /></a>
                </div>
                <div class="image">
                    <p id="UserOnlineStatus" style="color: #fff;float:right;">Online</p>
                </div>
                <div class="info-container">
                    <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo ucwords($getLoggedData['fname']);?></div>
                    <div class="email"><b>Reg No.</b> <?php echo $getLoggedData['registration_id'];?></div>
                    <div class="btn-group user-helper-dropdown">
                        <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="profile"><i class="material-icons">person</i>Profile</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="alumni-logout"><i class="material-icons">input</i>Sign Out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- #User Info -->
            <!-- Menu -->
            <div class="menu">
                <ul class="list">
                    <li class="header">MAIN NAVIGATION</li>
                    <?php
                        echo getDynamicLinks($dbh,$parentId = NULL);
                    ?>
                </ul>
            </div>
            <!-- #Menu -->
            <!-- Footer -->
            <div class="legal">
                <div class="copyright">
                    &copy; 2020 <a href="javascript:void(0);">All Rights Reserved</a>.
                </div>
            </div>
            <!-- #Footer -->
        </aside>
        <!-- #END# Left Sidebar -->
        <!-- Right Sidebar -->
        <aside id="rightsidebar" class="right-sidebar">
            <ul class="nav nav-tabs tab-nav-right" role="tablist">
                <li role="presentation" class="active"><a href="#skins" data-toggle="tab">SKINS</a></li>
                <li role="presentation"><a href="#settings" data-toggle="tab">SETTINGS</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active in active" id="skins">
                    <ul class="demo-choose-skin">
                        <li data-theme="red" class="active">
                            <div class="red"></div>
                            <span>Red</span>
                        </li>
                        <li data-theme="pink">
                            <div class="pink"></div>
                            <span>Pink</span>
                        </li>
                        <li data-theme="purple">
                            <div class="purple"></div>
                            <span>Purple</span>
                        </li>
                        <li data-theme="deep-purple">
                            <div class="deep-purple"></div>
                            <span>Deep Purple</span>
                        </li>
                        <li data-theme="indigo">
                            <div class="indigo"></div>
                            <span>Indigo</span>
                        </li>
                        <li data-theme="blue">
                            <div class="blue"></div>
                            <span>Blue</span>
                        </li>
                        <li data-theme="light-blue">
                            <div class="light-blue"></div>
                            <span>Light Blue</span>
                        </li>
                        <li data-theme="cyan">
                            <div class="cyan"></div>
                            <span>Cyan</span>
                        </li>
                        <li data-theme="teal">
                            <div class="teal"></div>
                            <span>Teal</span>
                        </li>
                        <li data-theme="green">
                            <div class="green"></div>
                            <span>Green</span>
                        </li>
                        <li data-theme="light-green">
                            <div class="light-green"></div>
                            <span>Light Green</span>
                        </li>
                        <li data-theme="lime">
                            <div class="lime"></div>
                            <span>Lime</span>
                        </li>
                        <li data-theme="yellow">
                            <div class="yellow"></div>
                            <span>Yellow</span>
                        </li>
                        <li data-theme="amber">
                            <div class="amber"></div>
                            <span>Amber</span>
                        </li>
                        <li data-theme="orange">
                            <div class="orange"></div>
                            <span>Orange</span>
                        </li>
                        <li data-theme="deep-orange">
                            <div class="deep-orange"></div>
                            <span>Deep Orange</span>
                        </li>
                        <li data-theme="brown">
                            <div class="brown"></div>
                            <span>Brown</span>
                        </li>
                        <li data-theme="grey">
                            <div class="grey"></div>
                            <span>Grey</span>
                        </li>
                        <li data-theme="blue-grey">
                            <div class="blue-grey"></div>
                            <span>Blue Grey</span>
                        </li>
                        <li data-theme="black">
                            <div class="black"></div>
                            <span>Black</span>
                        </li>
                    </ul>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="settings">
                    <div class="demo-settings">
                        <p>GENERAL SETTINGS</p>
                        <ul class="setting-list">
                            <li>
                                <span>Report Panel Usage</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                            <li>
                                <span>Email Redirect</span>
                                <div class="switch">
                                    <label><input type="checkbox"><span class="lever"></span></label>
                                </div>
                            </li>
                        </ul>
                        <p>SYSTEM SETTINGS</p>
                        <ul class="setting-list">
                            <li>
                                <span>Notifications</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                            <li>
                                <span>Auto Updates</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                        </ul>
                        <p>ACCOUNT SETTINGS</p>
                        <ul class="setting-list">
                            <li>
                                <span>Offline</span>
                                <div class="switch">
                                    <label><input type="checkbox"><span class="lever"></span></label>
                                </div>
                            </li>
                            <li>
                                <span>Location Permission</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </aside>
        <!-- #END# Right Sidebar -->
    </section>