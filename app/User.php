<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'last_name', 'email', 'password', 'gender', 'branch_office', 'address', 'city', 'country', 'phone_number', 'date_of_birth', 'blocked_date', 'state_id'
    ];
    protected $dates = [
     'blocked_date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

	public function roles(){
		return $this->belongsToMany('App\Role', 'user_role', 'user_id', 'role_id');
	}
  public function superiors()
  {
      return $this->hasMany('App\Personnel_in_charge', 'user_under_charge_id', 'id');
  }
  public function under_charge()
  {
      return $this->hasMany('App\Personnel_in_charge', 'user_at_charge_id', 'id');
  }
  public function config()
  {
      return $this->hasOne('App\User_config', 'user_id', 'id');
  }

//Permisos

  public function permissionViewProjects(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '1');
	}
  public function permissionCreateProject(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '2');
	}
  public function permissionDeleteProject(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '3');
	}
  public function permissionCreateElement(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '4');
	}
  public function permissionCreateProjectelement(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '5');
	}
  public function permissionDeleteElement(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '6');
	}
  public function permissionDeleteProjectelement(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '7');
	}
  public function permissionViewElementPrice(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '8');
	}
  public function permissionEditElementPrice(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '9');
	}
  public function permissionViewDisabledProjects(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '10');
	}
  public function permissionViewHiddenProjects(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '11');
	}
  public function permissionViewDisabledElements(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '12');
	}
  public function permissionViewHiddenElements(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '13');
	}
  public function permissionViewDeletedProjects(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '14');
  }
  public function permissionCreateSubset(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '15');
  }
  public function permissionDeleteSubset(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '16');
  }
  public function permissionViewDisabledSubsets(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '17');
  }
  public function permissionViewHiddenSubsets(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '18');
  }
  public function permissionViewDeletedSubsets(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '19');
  }
  public function permissionViewDeletedElements(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '20');
	}
  public function permissionViewElements(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '21');
	}
  public function permissionCreateAppliedElement(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '22');
	}
  public function permissionViewMaterials(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '23');
  }
  public function permissionViewDisabledMaterials(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '24');
	}
  public function permissionViewHiddenMaterials(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '25');
	}
  public function permissionViewDeletedMaterials(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '26');
	}
  public function permissionCreateMaterial(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '27');
	}
  public function permissionDeleteMaterial(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '28');
	}
  public function permissionViewOrder_types(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '29');
  }
  public function permissionViewDisabledOrder_types(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '30');
  }
  public function permissionViewHiddenOrder_types(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '31');
  }
  public function permissionViewDeletedOrder_types(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '32');
  }
  public function permissionCreateOrder_type(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '33');
  }
  public function permissionDeleteOrder_type(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '34');
  }
  public function permissionViewOperations(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '35');
  }
  public function permissionViewDisabledOperations(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '36');
  }
  public function permissionViewHiddenOperations(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '37');
  }
  public function permissionViewDeletedOperations(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '38');
  }
  public function permissionCreateOperation(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '39');
  }
  public function permissionDeleteOperation(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '40');
  }
  public function permissionViewSuppliers(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '41');
  }
  public function permissionViewDisabledSuppliers(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '42');
  }
  public function permissionViewHiddenSuppliers(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '43');
  }
  public function permissionViewDeletedSuppliers(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '44');
  }
  public function permissionCreateSupplier(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '45');
  }
  public function permissionDeleteSupplier(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '46');
  }
  public function permissionViewClients(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '47');
  }
  public function permissionViewDisabledClients(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '48');
  }
  public function permissionViewHiddenClients(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '49');
  }
  public function permissionViewDeletedClients(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '50');
  }
  public function permissionCreateClient(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '51');
  }
  public function permissionDeleteClient(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '52');
  }
  public function permissionViewElementsExt_f_1(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '53');
  }
  public function permissionViewSubsetsExt_f_1(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '54');
  }
  public function permissionViewProjectsExt_f_1(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '55');
  }
  public function permissionViewElementsExt_f_2(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '56');
  }
  public function permissionViewSubsetsExt_f_2(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '57');
  }
  public function permissionViewProjectsExt_f_2(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '58');
  }
  public function permissionViewElementsExt_f_3(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '59');
  }
  public function permissionViewSubsetsExt_f_3(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '60');
  }
  public function permissionViewProjectsExt_f_3(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '61');
  }
  public function permissionViewOperationPrice(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '62');
  }
  public function permissionViewProjectStats(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '63');
  }
  public function permissionCreateMaterialPrice(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '64');
  }
  public function permissionViewMaterialPrices(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '65');
  }
  public function permissionUseChat(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '66');
  }
  public function permissionViewPurchase_Orders(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '67');
  }
  public function permissionCreatePurchase_Order(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '68');
  }
  public function permissionDeletePurchase_Order(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '69');
  }
  public function permissionAwardPurchase_Order(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '70');
  }
  public function permissionReceivePurchase_Order(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '71');
  }
  public function permissionViewPurchase_OrderPrices(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '72');
  }
  public function permissionViewUsersBaseInfo(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '73');
  }
  public function permissionUseReminders(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '74');
  }
  public function permissionUseTasks(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '75');
  }
  public function permissionAssignTasks(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '76');
  }
  public function permissionViewSales(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '77');
  }
  public function permissionViewDisabledSales(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '78');
  }
  public function permissionViewHiddenSales(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '79');
  }
  public function permissionViewDeletedSales(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '80');
  }
  public function permissionCreateSale(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '81');
  }
  public function permissionDeleteSale(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '82');
  }
  public function permissionViewOwedItems(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '83');
  }
  public function permissionViewProjectFolder(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '84');
  }
  public function permissionViewElementFolder(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '85');
  }
  public function permissionViewOperationFolder(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '86');
  }
  public function permissionViewCash_Flow(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '87');
  }
  public function permissionEditSupplier_code(){
    return $this->hasOne('App\Permission', 'user_id', 'id')->where('code_id', '=', '88');
  }

}
