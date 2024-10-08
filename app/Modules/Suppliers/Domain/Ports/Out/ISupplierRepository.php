<?php

namespace App\Modules\Suppliers\Domain\Ports\Out;

use App\Modules\Suppliers\Domain\SupplierEntity;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Void_;

interface ISupplierRepository
{
    public function create($name, $ciuu, $phone, $userId, $id_type,$identification_number,$person_type,$company_name,$comercial_name, $email,
                           $web_page, $regimen, $retainer,$contributor, $ica,$rut, $copy_doc_represent,$bank_certification,$iso_9001):void;
    public function search(): array;
    public function index(): array;
    public function apply($invitation_id, $supplier_id, $status, $description, $payload): void;
    public function find($id):SupplierEntity;
    public function edit($name, $ciuu, $phone, $address,$userId,$id):void;

}
