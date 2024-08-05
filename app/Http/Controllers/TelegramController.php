<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Telegram\Bot\Api;


class TelegramController extends Controller
{
    protected $telegram;

    public function __construct()
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }

    public function handle(Request $request)
    {
        $update = $this->telegram->getWebhookUpdates();

        $message = $update->getMessage();
        $chatId = $message->getChat()->getId();
        $text = $message->getText();

        if ($text == '/start') {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => '🎉 777 TL Deneme Bonusu! 🎉\n\nYeter bu kadar, diğerleri sizi aldattı. En güvenli siteye başvurun ve bu müthiş fırsatı kaçırmayın!\n\n✅ %100 güvenli ve hızlı ödeme\n✅ Kullanıcı dostu arayüz\n✅ Harika bonuslar ve promosyonlar\n\nŞimdi katılın ve 777 TL deneme bonusunuzu alın! Tek yapmanız gereken aşağıdaki adımları izlemek:\n\n1. Aşağıdaki butona tıklayın.\n2. Telegram numaranızı girin ve onaylayın.\n3. Bonusunuzu almak için linke tıklayın.\n\n👉 /get_bonus',
            ]);
        }

        if ($text == '/get_bonus') {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Lütfen telefon numaranızı paylaşın.',
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [['text' => 'Telefon numaramı paylaş', 'request_contact' => true]],
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true,
                ]),
            ]);
        }

        if ($message->getContact()) {
            $phoneNumber = $message->getContact()->getPhoneNumber();
            $userId = $message->getFrom()->getId();

            // İstifadəçinin telefon nömrəsini bazaya əlavə edin
            User::updateOrCreate(
                ['telegram_id' => $userId],
                ['phone_number' => $phoneNumber]
            );

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Teşekkürler! Bonusunuzu almak için aşağıdaki linke tıklayın:\n\n👉 [Bonus Al](your_site_link)',
                'reply_markup' => json_encode([
                    'remove_keyboard' => true,
                ]),
            ]);
        }
    }

}
