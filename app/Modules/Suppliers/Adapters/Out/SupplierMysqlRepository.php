<?php

namespace App\Modules\Suppliers\Adapters\Out;

use App\Models\Document;
use App\Modules\Invitations\Adapters\Out\Invitation;
use App\Modules\Suppliers\Domain\Ports\Out\ISupplierRepository;
use App\Modules\Suppliers\Domain\SupplierEntity;
use App\Modules\Users\Domain\ports\Out\IUserRepository;
use App\Modules\Users\Adapters\Out\User;
use App\Modules\Users\Domain\UserEntity;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Void_;
use function Laravel\Prompts\table;

class SupplierMysqlRepository implements ISupplierRepository
{

    public function create($name, $ciuu, $phone, $userId, $id_type,$identification_number,$person_type,$company_name,$comercial_name, $email,
                           $web_page, $regimen, $retainer,$contributor, $ica,$rut, $copy_doc_represent,$bank_certification,$iso_9001 ): void
    {
        $suppId = auth()->user()->supplier->id;
        Supplier::updateOrCreate(['id'=>$suppId], [ 'ciuu' => $ciuu, 'phone' => $phone,'person_type'=>$person_type,'company_name'=>$company_name, 'comercial_name'=>$comercial_name,
           'web_page'=>$web_page, 'regimen'=>$regimen,'retainer'=>$retainer,  'contributor'=>$contributor, 'ica'=>$ica, 'rut'=>$rut, 'copy_doc_represent'=>$copy_doc_represent, 'bank_certification'=>$bank_certification, 'iso_9001'=>$iso_9001]);

    }
    public function search(): array
    {
        return Supplier::get()->all();
    }

    public function index(): array
    {
        return DB::table('suppliers')->get()->all();
    }

    public function edit($name, $ciuu, $phone, $address, $userId, $id): void
    {
        Supplier::updateFrom(['name' => $name, 'ciuu' => $ciuu, 'phone' => $phone, 'address' => $address, 'user_id' => $userId, 'id' => $id]);
        //TODO: revisar
    }
    public function find($id): SupplierEntity
    {
        $supplier = Supplier::find($id);
        return new SupplierEntity($supplier->toArray());
    }
    public function apply($invitation_id, $supplier_id, $status, $description, $payload): void
    {
        $supplier = Supplier::find($supplier_id);
        $supplier->invitations()->attach($invitation_id, [
            'status' => $status,
            'description' => $description,
            'payload'=>$payload]);
    }

}
