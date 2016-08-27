<?php
/*
 * Copyright 2016 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

class Google_Service_IdentityToolkit_IdentitytoolkitRelyingpartyResetPasswordRequest extends Google_Model
{
  public $email;
  public $newPassword;
  public $oldPassword;
  public $oobCode;

  public function setEmail($email)
  {
    $this->email = $email;
  }
  public function getEmail()
  {
    return $this->email;
  }
  public function setNewPassword($newPassword)
  {
    $this->newPassword = $newPassword;
  }
  public function getNewPassword()
  {
    return $this->newPassword;
  }
  public function setOldPassword($oldPassword)
  {
    $this->oldPassword = $oldPassword;
  }
  public function getOldPassword()
  {
    return $this->oldPassword;
  }
  public function setOobCode($oobCode)
  {
    $this->oobCode = $oobCode;
  }
  public function getOobCode()
  {
    return $this->oobCode;
  }
}