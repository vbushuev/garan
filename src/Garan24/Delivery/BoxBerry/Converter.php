<?php
namespace Garan24\Delivery\BoxBerry;
use \Garan24\Deal\Deal as Deal;
class Converter{
    protected $bb_data = false;
    protected $grn_data;
    public function __construct(){

    }
    public function convert($o){
        if(!$o instanceof Deal) {
            throw new \Exception("Wrong object");
        }
        $this->grn_data = $o;
        $this->bb_data=[];
        //$this->bb_data['updateByTrack']='Трекинг-код ранее созданной посылки';
        $this->bb_data['order_id']=$o->order->internal_order_id;//'ID заказа в ИМ';
        //$this->bb_data['PalletNumber']='Номер палеты';
        //$this->bb_data['barcode']='Штрих-код заказа';
        $this->bb_data['price']=$o->order->order_total;//'Объявленная стоимость';
        $this->bb_data['payment_sum']=0;//'Сумма к оплате';
        $this->bb_data['delivery_sum']=0;//'Стоимость доставки';
        $this->bb_data['vid']=1;//'Тип доставки (1/2)';
        $this->bb_data['shop']=[
           'name'=>'Код ПВЗ',
           'name1'=>'Код пункта поступления'
        ];
        $this->bb_data['customer']=array(
            'fio'=>'ФИО получателя',
            'phone'=>'Номер телефона',
            'phone2'=>'Доп. номер телефона',
            'email'=>'E-mail для оповещений',
            'name'=>'Наименование организации',
            'address'=>'Адрес',
            'inn'=>'ИНН',
            'kpp'=>'КПП',
            'r_s'=>'Расчетный счет',
            'bank'=>'Наименование банка',
            'kor_s'=>'Кор. счет',
            'bik'=>'БИК'
        );
        $this->bb_data['kurdost'] = array(
            'index' => 'Индекс',
            'citi' => 'Город',
            'addressp' => 'Адрес получателя',
            'timesfrom1' => 'Время доставки, от',
            'timesto1' => 'Время доставки, до',
            'timesfrom2' => 'Альтернативное время, от',
            'timesto2' => 'Альтернативное время, до',
            'timep' => 'Время доставки текстовый формат',
            'comentk' => 'Комментарий'
        );
        $this->bb_data['items']=array(
            array(
                'id'=>'ID товара в БД ИМ',
                'name'=>'Наименование товара',
                'UnitName'=>'Единица измерения',
                'nds'=>'Процент НДС',
                'price'=>'Цена товара',
                'quantity'=>'Количество'
            )
        );
        $this->bb_data['weights']=array(
            'weight'=>'Вес 1-ого места',
            'weight2'=>'Вес 2-ого места',
            'weight3'=>'Вес 3-его места',
            'weight4'=>'Вес 4-ого места',
            'weight5'=>'Вес 5-ого места'
        );
        return $this->bb_data;
    }
};
?>
