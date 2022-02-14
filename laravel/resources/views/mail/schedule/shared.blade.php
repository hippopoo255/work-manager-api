@component('mail::message')
{{ $notifiable->full_name }}さん<br />
{{ config('app.name') }}をご利用いただきありがとうございます。<br />
{{ $schedule->createdBy->full_name }}さんより新しいスケジュールが共有されました。<br />

# スケジュール概要

@component('mail::table')
| 件名 | 開始日時 | 終了日時 | 公開設定 |
| ------------- |:-------------:|:--------:|:--------:|
| {{ $schedule->title }} | {{ $schedule->start->format('n月j日 G:i') }} | {{ $schedule->end->format('n月j日 G:i') }} | {{ $schedule->is_public ? '公開' : '非公開' }} |
@endcomponent

@component('mail::button', ['url' => $detailUrl, 'color' => 'green'])
スケジュールカレンダーを見る
@endcomponent

このメールアドレスは送信専用となっております。本メールに対するご返信には対応致しかねますのでご了承下さい。
@endcomponent
