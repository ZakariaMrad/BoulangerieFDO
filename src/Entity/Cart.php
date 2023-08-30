<?php
namespace App\Entity;

use App\Core\Constants;
use App\Entity\Purchase;

class Cart
{
    private $purchases = [];
    private $BASEQUANTITYTOADD =1;

    private $isLocked= false;

    public function __construct(){
        $this->purchases=[];
    }

    public function add(Product $product):string //true if is added, false if updated
    {
        $purchaseId = $this->ContainsPurchases($product->getIdProduct()); //If is -1, then purchase is not in array

        if ($purchaseId ==-1) {
            $purchase = new Purchase($product);
            $this->purchases[]=$purchase;
            return "added";
        } else {
            $baseQantity = $this->purchases[$purchaseId]->getQuantity();
            $this->purchases[$purchaseId]->setQuantity($baseQantity+$this->BASEQUANTITYTOADD);
            return "updated";
        }
    }

    public function update($post)
    {
        $inpQuantity = $post['inpQuantity'];
        for ($i=0; $i < count($inpQuantity); $i++) { 
            $this->purchases[$i]->setQuantity($inpQuantity[$i]);
        }
    }

    public function empty(){
        $this->purchases=[];
    }

    public function removeOne($idPurchase){
        array_splice($this->purchases,$idPurchase,1);
    }
    public function getPurchases(){
        return $this->purchases;
    }
    public function getSubTotalPrice(){
        return array_reduce($this->purchases, function($sum, $item)
        {
            return $sum + $item->getPurchasePrice();
        });
    }
    public function getTps(){
        return $this->getSubTotalPrice()*Constants::TPS;
    }
    public function getTvq(){
        return $this->getSubTotalPrice()*Constants::TVQ;
    }
    public function getShippingCost(){
        if ($this->getSubTotalPrice()>0) {
            return Constants::SHIPPING_COST;
        }
        return 0;
    }
    public function getTotalPrice(){
        return $this->getSubTotalPrice()+$this->getTps()+$this->getTvq()+$this->getShippingCost();
    }
    public function getTotalPriceStripe():int{
        return $this->getTotalPrice()*Constants::STRIPE_FORMAT;
    }
    public function getIsLocked(){
        return $this->isLocked;
    }

    public function setIsLocked(bool $value){
        $this->isLocked = $value;
    }

    private function ContainsPurchases($idProduct): int
    {
        for ($i = 0; $i < count($this->purchases); $i++) {
            if ($this->purchases[$i]->getProduct()->getIdProduct() == $idProduct) {
                return $i;
            }
        }
        return -1;
    }
}