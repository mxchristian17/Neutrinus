<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
		$this->call(RoleTableSeeder::Class);
		$this->call(UserTableSeeder::Class);
		$this->call(ProjectTableSeeder::Class);
    $this->call(ElementTableSeeder::Class);
    $this->call(SubsetTableSeeder::Class);
    $this->call(ProjectelementTableSeeder::Class);
    $this->call(MaterialTableSeeder::Class);
    $this->call(Order_typeTableSeeder::Class);
    $this->call(General_stateTableSeeder::Class);
    $this->call(Specific_stateTableSeeder::Class);
    $this->call(OperationTableSeeder::Class);
    $this->call(Operation_nameTableSeeder::Class);
    $this->call(SupplierTableSeeder::Class);
    $this->call(Supplier_contactTableSeeder::Class);
    $this->call(ClientTableSeeder::Class);
    $this->call(Client_contactTableSeeder::Class);
    $this->call(StateTableSeeder::Class);
    $this->call(PermissionTableSeeder::Class);
    $this->call(ProjecttypeTableSeeder::Class);
    $this->call(Material_priceTableSeeder::Class);
    $this->call(MessageTableSeeder::Class);
    $this->call(PurchaseTableSeeder::Class);
    $this->call(Purchase_elementTableSeeder::Class);
    $this->call(Purchase_projectTableSeeder::Class);
    $this->call(Item_supplierTableSeeder::Class);
    $this->call(CurrencyTableSeeder::Class);
    $this->call(SaleTableSeeder::Class);
    $this->call(ReminderTableSeeder::Class);
    $this->call(TaskTableSeeder::Class);
    $this->call(Personnel_in_chargeTableSeeder::Class);
    $this->call(User_configTableSeeder::Class);
    $this->call(TodoTableSeeder::Class);
    $this->call(TaxTableSeeder::Class);
    $this->call(Supplier_codeTableSeeder::Class);
    }
}
