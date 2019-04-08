<?php

/**
 * Bitragem's official php lib
 * https://bitragem.com/
 */
namespace bitragem;

class Bitragem {

    /**
     * Get Array from JSON URL
     * @param String $input
     * @return Array
     * Ex.:
     * get_url_contents('https://bitragem.com/api/v1/');
     */
    public function get_url_contents($input) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $input);
        curl_setopt($ch, CURLOPT_REFERER, $input);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36");
        $result = curl_exec($ch);
        curl_close($ch);
        return (json_decode($result, true));
    }
    /**
     * Get Array from JSON File
     * @param String $input
     * @return Array
     * Ex.:
     * get_file_contents('config.json');
     */
    public function get_file_contents($input) {
        $json = file_get_contents($input);
        return (json_decode($json, true));
    }
}

class _3xbit extends Bitragem{
    static private $assets = array('BTC','ETH','LTC','DASH');
    /**
    * Get Array of orders
    * @param String $asset
    * @return Array
    * Ex.:
    * getBook('BTC');
    */
    public function getBook($asset){
        if (in_array($asset, self::$assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://api.exchange.3xbit.com.br/v1/orderbook/credit/'.$asset);
        $ticker = self::get_url_contents('https://api.exchange.3xbit.com.br/ticker/brl/');
        
        for ($i = 0; $i < count($book['sell_orders']); $i++) {
            $newBook['asks'][] = array(
                'price' => $book['sell_orders'][$i]['unit_price'] * $ticker['CREDIT_'.$asset]['dollar_brl'],
                'volume' => $book['sell_orders'][$i]['quantity']);
        }
        for ($i = 0; $i < count($book['buy_orders']); $i++) {
            $newBook['bids'][] = array(
                'price' => $book['buy_orders'][$i]['unit_price'] * $ticker['CREDIT_'.$asset]['dollar_brl'],
                'volume' => $book['buy_orders'][$i]['quantity']);
        }

        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class acasadobitcoin extends Bitragem{
    static private $assets = array('BTC', 'ETH');
    public function getBook($asset){
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://apicasabitcoinprod.alphapoint.com:8443/AP/GetL2Snapshot?OMSId=1&InstrumentId=1&Depth=1');
        
        for ($i = 0; $i < count($book); $i++) {
            if ($book[$i][9] == 1) {
                $newBook['asks'][] = array('price' => $book[$i][6],
                    'volume' => $book[$i][8]);
            }
            if ($book[$i][9] == 0) {
                $newBook['bids'][] = array('price' => $book[$i][6],
                    'volume' => $book[$i][8]);
            }
        }

        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class bitblue extends Bitragem{
    static private $assets = array('BTC','ETH','XRP', 'DASH');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;
        
        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://bitblue.com/api/order-book?currency=BRL&market='.$asset);
        
        for ($i = 0; $i < count($book['order-book']['ask']); $i++) {
            $newBook['asks'][] = array('price' => $book['order-book']['ask'][$i]['price'],
                'volume' => $book['order-book']['ask'][$i]['order_amount']);
        }
        for ($i = 0; $i < count($book['order-book']['bid']); $i++) {
            $newBook['bids'][] = array('price' => $book['order-book']['bid'][$i]['price'],
                'volume' => $book['order-book']['bid'][$i]['order_amount']);
        }

        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class bitinka extends Bitragem{
    static private $assets = array('BTC', 'ETH');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://www.bitinka.com/api/apinka/order_book/'.$asset.'_BRL?format=json');
        
        for ($i = 0; $i < count($book['orders']['purchases']['BRL']); $i++) {
            $newBook['asks'][] = array('price' => $book['orders']['purchases']['BRL'][$i]['Price'],
                'volume' => $book['orders']['purchases']['BRL'][$i]['Amount']);
        }
        for ($i = 0; $i < count($book['orders']['sales']['BRL']); $i++) {
            $newBook['bids'][] = array('price' => $book['orders']['sales']['BRL'][$i]['Price'],
                'volume' => $book['orders']['sales']['BRL'][$i]['Amount']);
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class bitcambio extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://bitcambio_api.blinktrade.com/api/v1/BRL/orderbook');
        
        for ($i = 0; $i < count($book['asks']); $i++) {
            $newBook['asks'][] = array('price' => $book['asks'][$i][0],
                'volume' => $book['asks'][$i][1]);
        }
        for ($i = 0; $i < count($book['bids']); $i++) {
            $newBook['bids'][] = array('price' => $book['bids'][$i][0],
                'volume' => $book['bids'][$i][1]);
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class bitcointoyou extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://www.bitcointoyou.com/api/orderbook.aspx');
        
        for ($i = 0; $i < count($book['asks']); $i++) {
            $newBook['asks'][] = array('price' => $book['asks'][$i][0],
                'volume' => $book['asks'][$i][1]);
        }
        for ($i = 0; $i < count($book['bids']); $i++) {
            $newBook['bids'][] = array('price' => $book['bids'][$i][0],
                'volume' => $book['bids'][$i][1]);
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class bitnuvem extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://bitnuvem.com/api/BTC/orderbook');
        
        for ($i = 0; $i < count($book['asks']); $i++) {
            $newBook['asks'][] = array('price' => floatval($book['asks'][$i][0]),
                'volume' => floatval($book['asks'][$i][1]));
        }
        for ($i = 0; $i < count($book['bids']); $i++) {
            $newBook['bids'][] = array('price' => floatval($book['bids'][$i][0]),
                'volume' => floatval($book['bids'][$i][1]));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class bitpreco extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://cors.io/?https://api.bitpreco.com/btc-brl/orderbook');
        
        for ($i = 0; $i < count($book['asks']); $i++) {
            $newBook['asks'][] = array('price' => floatval($book['asks'][$i]['price']),
                'volume' => floatval($book['asks'][$i]['amount']));
        }
        for ($i = 0; $i < count($book['bids']); $i++) {
            $newBook['bids'][] = array('price' => floatval($book['bids'][$i]['price']),
                'volume' => floatval($book['bids'][$i]['amount']));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class bitrecife extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://exchange.bitrecife.com.br/api/v3/public/getorderbook?market=BTC_BRL');
        
        for ($i = 0; $i < count($book['result']['sell']); $i++) {
            $newBook['asks'][] = array('price' => floatval($book['result']['sell'][$i]['Rate']),
                'volume' => floatval($book['result']['sell'][$i]['Quantity']));
        }
        for ($i = 0; $i < count($book['result']['buy']); $i++) {
            $newBook['bids'][] = array('price' => floatval($book['result']['buy'][$i]['Rate']),
                'volume' => floatval($book['result']['buy'][$i]['Quantity']));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class bitcointrade extends Bitragem{
    static private $assets = array('BTC','ETH','LTC' );
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://api.bitcointrade.com.br/v2/public/BRL'.$asset.'/orders');
        
        for ($i = 0; $i < count($book['data']['asks']); $i++) {
            $newBook['asks'][] = array('price' => $book['data']['asks'][$i]['unit_price'],
                'volume' => $book['data']['asks'][$i]['amount']);
        }
        for ($i = 0; $i < count($book['data']['bids']); $i++) {
            $newBook['bids'][] = array('price' => $book['data']['bids'][$i]['unit_price'],
                'volume' => $book['data']['bids'][$i]['amount']);
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class brabex extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://exchange.brabex.com.br/api/v1/BRL/orderbook?crypto_currency=BTC');
        
        for ($i = 0; $i < count($book['asks']); $i++) {
            $newBook['asks'][] = array('price' => floatval($book['asks'][$i][0]),
                'volume' => floatval($book['asks'][$i][1]));
        }
        for ($i = 0; $i < count($book['bids']); $i++) {
            $newBook['bids'][] = array('price' => floatval($book['bids'][$i][0]),
                'volume' => floatval($book['bids'][$i][1]));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class brasilbitcoin extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://brasilbitcoin.com.br/API/orderbook/BTC');
        
        for ($i = 0; $i < count($book['sell']); $i++) {
            $newBook['asks'][] = array('price' => floatval($book['sell'][$i]['preco']),
                'volume' => floatval($book['sell'][$i]['quantidade']));
        }
        for ($i = 0; $i < count($book['buy']); $i++) {
            $newBook['bids'][] = array('price' => floatval($book['buy'][$i]['preco']),
                'volume' => floatval($book['buy'][$i]['quantidade']));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class braziliex extends Bitragem{
    static private $assets = array('BTC', 'ETH', 'LTC', 'DASH');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://braziliex.com/api/v1/public/orderbook/'.strtolower($asset).'_brl');
        
        for ($i = 0; $i < count($book['asks']); $i++) {
            $newBook['asks'][] = array('price' => $book['asks'][$i]['price'],
                'volume' => $book['asks'][$i]['amount']);
        }
        for ($i = 0; $i < count($book['bids']); $i++) {
            $newBook['bids'][] = array('price' => $book['bids'][$i]['price'],
                'volume' => $book['bids'][$i]['amount']);
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class btcbolsa extends Bitragem{
    static private $assets = array('BTC', 'ETH', 'LTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://apiv2.btcbolsa.com/v2/orders/order-book/'.$asset.'_BRL');
        
        for ($i = count($book['data']['asks']); $i > 0; $i--) {
            $newBook['asks'][] = array(
                'price' => $book['data']['asks'][$i]['price_unity'],
                'volume' => $book['data']['asks'][$i]['amount']);
        }
        for ($i = 0; $i < count($book['data']['bids']); $i++) {
            $newBook['bids'][] = array(
                'price' => $book['data']['bids'][$i]['price_unity'],
                'volume' => $book['data']['bids'][$i]['amount']);
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class coinbene extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('http://api.coinbene.com/v1/market/orderbook?symbol=btcbrl');
        
        for ($i = 0; $i < count($book['orderbook']['asks']); $i++) {
            $newBook['asks'][] = array('price' => $book['orderbook']['asks'][$i]['price'],
                'volume' => $book['orderbook']['asks'][$i]['quantity']);
        }
        for ($i = 0; $i < count($book['orderbook']['bids']); $i++) {
            $newBook['bids'][] = array('price' => $book['orderbook']['bids'][$i]['price'],
                'volume' => $book['orderbook']['bids'][$i]['quantity']);
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class coin2001 extends Bitragem{
    static private $assets = array('BTC', 'ETH', 'LTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://coin2001.com/api/v1/public/getOrderBook?market=BRL_'.$asset.'&type=both');
        
        for ($i = 0; $i < count($book['result']['sell']); $i++) {
            $newBook['asks'][] = array('price' => $book['result']['sell'][$i]['Rate'],
                'volume' => $book['result']['sell'][$i]['Quantity']);
        }
        for ($i = 0; $i < count($book['result']['buy']); $i++) {
            $newBook['bids'][] = array('price' => $book['result']['buy'][$i]['Rate'],
                'volume' => $book['result']['buy'][$i]['Quantity']);
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class coinext extends Bitragem{
    static private $assets = array('BTC', 'ETH', 'LTC', 'XRP');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;
        switch ($asset) {
            case 'BTC':
                $asset = 1;
                break;
            case 'ETH':
                $asset = 4;
                break;
            case 'LTC':
                $asset = 2;
                break;
            case 'XRP':
                $asset = 6;
                break;
        }

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://api.coinext.com.br:8443/AP/GetL2Snapshot?OMSId=1&InstrumentId='.$asset.'&Depth=1');
        
        for ($i = 0; $i < count($book); $i++) {
            if ($book[$i][9] == 1) {
                $newBook['asks'][] = array('price' => $book[$i][6],
                    'volume' => $book[$i][8]);
            }
            if ($book[$i][9] == 0) {
                $newBook['bids'][] = array('price' => $book[$i][6],
                    'volume' => $book[$i][8]);
            }
    
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class citcoin extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://api.citcoin.com.br/v1/btc/orderbook/');
        
        for ($i = 0; $i < count($book['ask']); $i++) {
            $newBook['asks'][] = array(
                'price' => floatval($book['ask'][$i]['btc_price']),
                'volume' => floatval($book['ask'][$i]['btc']));
        }
        for ($i = 0; $i < count($book['bid']); $i++) {
            $newBook['bids'][] = array(
                'price' => floatval($book['bid'][$i]['btc_price']),
                'volume' => floatval($book['bid'][$i]['btc']));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class cryptomarket extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $bookAsk = self::get_url_contents('https://api.cryptomkt.com/v1/book?market=BTCBRL&type=sell&page=0');
        $bookBid = self::get_url_contents('https://api.cryptomkt.com/v1/book?market=BTCBRL&type=buy&page=0');

        for ($i = 0; $i < count($bookAsk['data']); $i++) {
            $newBook['asks'][] = array(
                'price' => floatval($bookAsk['data'][$i]['price']),
                'volume' => floatval($bookAsk['data'][$i]['amount']));
        }
        for ($i = 0; $i < count($bookBid['data']); $i++) {
            $newBook['bids'][] = array(
                'price' => floatval($bookBid['data'][$i]['price']),
                'volume' => floatval($bookBid['data'][$i]['amount']));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class flowbtc extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://publicapi.flowbtc.com.br/v1/book/BTCBRL?depth=3000');
        
        for ($i = 0; $i < count($book['data']['asks']); $i++) {
            $newBook['asks'][] = array('price' => floatval($book['data']['asks'][$i]['Price']),
                'volume' => floatval($book['data']['asks'][$i]['Quantity']));
        }
        for ($i = 0; $i < count($book['data']['bids']); $i++) {
            $newBook['bids'][] = array('price' => floatval($book['data']['bids'][$i]['Price']),
                'volume' => floatval($book['data']['bids'][$i]['Quantity']));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class foxbit extends Bitragem{
    static private $assets = array('BTC', 'ETH', 'LTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        switch ($asset) {
            case 'BTC':
                $asset = 1;
                break;
            case 'ETH':
                $asset = 4;
                break;
            case 'LTC':
                $asset = 2;
                break;
        }

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://apifoxbitprodlb.alphapoint.com:8443/AP/GetL2Snapshot?OMSId=1&InstrumentId='.$asset.'&Depth=1');
        
        for ($i = 0; $i < count($book); $i++) {
            if ($book[$i][9] == 1) {
                $newBook['asks'][] = array('price' => $book[$i][6],
                    'volume' => $book[$i][8]);
            }
            if ($book[$i][9] == 0) {
                $newBook['bids'][] = array('price' => $book[$i][6],
                    'volume' => $book[$i][8]);
            }
    
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class intertradec extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://app.intertradec.com.br/api/v1/public/booking/BTC_BRL');
        
        for ($i = 0; $i < count($book); $i++) {
            if ($book[$i]['type'] == 'SELL') {
                $newBook['asks'][] = array(
                    'price' => floatval($book[$i]['rate']),
                    'volume' => floatval($book[$i]['amount']));
            }
            if ($book[$i]['type'] == 'BUY') {
                $newBook['bids'][] = array(
                    'price' => floatval($book[$i]['rate']),
                    'volume' => floatval($book[$i]['amount']));
            }
    
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class mercadobitcoin extends Bitragem{
    static private $assets = array('BTC', 'ETH', 'LTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://www.mercadobitcoin.net/api/'.$asset.'/orderbook/');
        
        for ($i = 0; $i < count($book['asks']); $i++) {
            $newBook['asks'][] = array('price' => floatval($book['asks'][$i][0]),
                'volume' => floatval($book['asks'][$i][1]));
        }
        for ($i = 0; $i < count($book['bids']); $i++) {
            $newBook['bids'][] = array('price' => floatval($book['bids'][$i][0]),
                'volume' => floatval($book['bids'][$i][1]));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class modiax extends Bitragem{
    static private $assets = array('BTC', 'XRP');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://app.modiax.com/api/v2/order_book?market='.strtolower($asset).'brl');
        
        for ($i = 0; $i < count($book['asks']); $i++) {
            $newBook['asks'][] = array('price' => floatval($book['asks'][$i]['price']),
                'volume' => floatval($book['asks'][$i]['volume']));
        }
        for ($i = 0; $i < count($book['bids']); $i++) {
            $newBook['bids'][] = array('price' => floatval($book['bids'][$i]['price']),
                'volume' => floatval($book['bids'][$i]['volume']));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class negociecoins extends Bitragem{
    static private $assets = array('BTC', 'ETH', 'LTC', 'DASH', 'BCH', 'BTG');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://broker.negociecoins.com.br/api/v3/'.$asset.'BRL/orderbook');
        
        for ($i = 0; $i < count($book['ask']); $i++) {
            $newBook['asks'][] = array('price' => floatval($book['ask'][$i]['price']),
                'volume' => floatval($book['ask'][$i]['quantity']));
        }
        for ($i = 0; $i < count($book['bid']); $i++) {
            $newBook['bids'][] = array('price' => floatval($book['bid'][$i]['price']),
                'volume' => floatval($book['bid'][$i]['quantity']));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class omnitrade extends Bitragem{
    static private $assets = array('BTC', 'ETH', 'LTC', 'DASH');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://omnitrade.io/api/v2/order_book?market='.strtolower($asset).'brl&asks_limit=200&bids_limit=200');
        
        for ($i = 0; $i < count($book['asks']); $i++) {
            $newBook['asks'][] = array(
                'price' => floatval($book['asks'][$i]['price']),
                'volume' => floatval($book['asks'][$i]['volume']));
        }
        for ($i = 0; $i < count($book['bids']); $i++) {
            $newBook['bids'][] = array(
                'price' => floatval($book['bids'][$i]['price']),
                'volume' => floatval($book['bids'][$i]['volume']));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class pagcripto extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://api.pagcripto.com.br/v1/BTC/orders');
        
        for ($i = 0; $i < count($book['data']['asks']); $i++) {
            $newBook['asks'][] = array(
                'price' => floatval($book['data']['asks'][$i]['cot']),
                'volume' => floatval($book['data']['asks'][$i]['qtd']));
        }
        for ($i = 0; $i < count($book['data']['bids']); $i++) {
            $newBook['bids'][] = array(
                'price' => floatval($book['data']['bids'][$i]['cot']),
                'volume' => floatval($book['data']['bids'][$i]['qtd']));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class pitaiatrade extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://api.pitaiatrade.com/v1/orderbook');
        
        for ($i = 0; $i < count($book['asks']); $i++) {
            $newBook['asks'][] = array(
                'price' => $book['asks'][$i][1],
                'volume' => $book['asks'][$i][0]);
        }
        for ($i = 0; $i < count($book['bids']); $i++) {
            $newBook['bids'][] = array(
                'price' => $book['bids'][$i][1],
                'volume' => $book['bids'][$i][0]);
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class profitfy extends Bitragem{
    static private $assets = array('BTC', 'DCR');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://profitfy.trade/api/v1/public/orderbook/BRL/'.strtolower($asset));
        
        for ($i = 0; $i < count($book[0]['sell']); $i++) {
            $newBook['asks'][] = array('price' => $book[0]['sell'][$i]['price'],
                'volume' => $book[0]['sell'][$i]['amount']);
        }
        for ($i = 0; $i < count($book[0]['buy']); $i++) {
            $newBook['bids'][] = array('price' => $book[0]['buy'][$i]['price'],
                'volume' => $book[0]['buy'][$i]['amount']);
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class satoshitango extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://api.satoshitango.com/v3/ticker/BRL');
        
        $newBook['asks'][] = array(
            'price' => floatval($book['data']['ticker']['BTC']['ask']),
            'volume' => 100);
    
        $newBook['bids'][] = array(
            'price' => floatval($book['data']['ticker']['BTC']['bid']),
            'volume' => 100);
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class tembtc extends Bitragem{
    static private $assets = array('BTC', 'LTC', 'BCH');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://broker.tembtc.com.br/api/v3/'.$asset.'BRL/orderbook');
        
        for ($i = 0; $i < count($book['ask']); $i++) {
            $newBook['asks'][] = array('price' => floatval($book['ask'][$i]['price']),
                'volume' => floatval($book['ask'][$i]['quantity']));
        }
        for ($i = 0; $i < count($book['bid']); $i++) {
            $newBook['bids'][] = array('price' => floatval($book['bid'][$i]['price']),
                'volume' => floatval($book['bid'][$i]['quantity']));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class walltime extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $data = getFromUrl('https://s3.amazonaws.com/data-production-walltime-info/production/dynamic/meta.json?now=1517922306634.319625.92');
        $book = getFromUrl('https://s3.amazonaws.com/data-production-walltime-info/production/dynamic/order-book/v8878cb_r' . $data["current_round"] . '_p0.json?now=1517922306795.319787.02');
        
        for ($i = 0; $i < count($book['xbt-brl']); $i++) {
            $newBook['asks'][] = array('price' => eval('return ' . $book['xbt-brl'][$i][1] . ';') / eval('return ' . $book['xbt-brl'][$i][0] . ';'),
                'volume' => eval('return ' . $book['xbt-brl'][$i][0] . ';'));
        }
        for ($i = 0; $i < count($book['brl-xbt']); $i++) {
            $newBook['bids'][] = array('price' => eval('return ' . $book['brl-xbt'][$i][0] . ';') / eval('return ' . $book['brl-xbt'][$i][1] . ';'),
                'volume' => eval('return ' . $book['brl-xbt'][$i][1] . ';'));
        }

        $book = getFromUrl('https://s3.amazonaws.com/data-production-walltime-info/production/dynamic/order-book/v8878cb_r' . $data["current_round"] . '_p1.json?now=1517922306795.319787.02');
        
        for ($i = 0; $i < count($book['xbt-brl']); $i++) {
            $newBook['asks'][] = array('price' => eval('return ' . $book['xbt-brl'][$i][1] . ';') / eval('return ' . $book['xbt-brl'][$i][0] . ';'),
                'volume' => eval('return ' . $book['xbt-brl'][$i][0] . ';'));
        }
        for ($i = 0; $i < count($book['brl-xbt']); $i++) {
            $newBook['bids'][] = array('price' => eval('return ' . $book['brl-xbt'][$i][0] . ';') / eval('return ' . $book['brl-xbt'][$i][1] . ';'),
                'volume' => eval('return ' . $book['brl-xbt'][$i][1] . ';'));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

class welcoin extends Bitragem{
    static private $assets = array('BTC');
    public function getBook($asset){
        if (in_array($asset, $assets) == false) return null;

        $newBook['asset'] = $asset;
        $newBook['base'] = 'BRL';
        $book = self::get_url_contents('https://broker.welcoin.com.br/api/v3/btcbrl/orderbook');
        
        for ($i = 0; $i < count($book['ask']); $i++) {
            $newBook['asks'][] = array('price' => floatval($book['ask'][$i]['price']),
                'volume' => floatval($book['ask'][$i]['quantity']));
        }
        for ($i = 0; $i < count($book['bid']); $i++) {
            $newBook['bids'][] = array('price' => floatval($book['bid'][$i]['price']),
                'volume' => floatval($book['bid'][$i]['quantity']));
        }
        
        return $newBook;
    }
    public function getAssets() { return self::$assets; }
}

?>