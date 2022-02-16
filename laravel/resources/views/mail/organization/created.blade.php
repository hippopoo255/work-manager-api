@component('mail::message')
{{ $notifiable->full_name }}さん<br /><br />
{{ config('app.name') }}をご利用いただきありがとうございます。<br />
当サービスの開通と、 **管理者用システム** のアカウント登録が完了しましたのでご連絡いたします。<br /><br />
**※このメールは組織情報のご登録が完了したユーザの方にご案内しております。** <br /><br />

@component('mail::button', ['url' => $mypageUrl, 'color' => 'green'])
当サービスのマイページはこちら
@endcomponent

管理システムにログインいただく前に、以下のリンク先よりアカウントの検証が必要となります。<br />
アカウントの検証は、別途メールで送信される **検証コード** を入力して下さい。<br />

@component('mail::button', ['url' => $adminFrontUrl, 'color' => 'primary'])
管理者用システムのアカウント検証はこちら
@endcomponent

検証が完了しましたら、組織情報のご登録時に入力いただいたパスワードを使って、ログインができるようになります。<br />

@component('mail::button', ['url' => $adminSigninUrl, 'color' => 'primary'])
管理者用システムのログインはこちら
@endcomponent

今後とも、当サービスをご愛顧賜りますよう、よろしくお願い申し上げます。

このメールアドレスは送信専用となっております。本メールに対するご返信には対応致しかねますのでご了承下さい。
@endcomponent
