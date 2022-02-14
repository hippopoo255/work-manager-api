@component('mail::message')
{{ $notifiable->full_name }}さん<br />
{{ config('app.name') }}をご利用いただきありがとうございます。<br />
{{ $meetingRecord->createdBy->full_name }}さんが新しい議事録を追加しました。<br /><br />

※このメールは「参加者」として加えられたメンバーに送信されています。<br /><br />

# 議事録概要

@component('mail::table')
| 会議名 | 開催日時 | 開催場所 |
| ------------- |:-------------:| :--------:|
| {{ $meetingRecord->title }} | {{ $meetingRecord->meeting_date->format('n月j日 G:i') }} | {{ $meetingRecord->place->name }} |
@endcomponent
@component('mail::button', ['url' => $detailUrl, 'color' => 'green'])
詳細の確認
@endcomponent

このメールアドレスは送信専用となっております。本メールに対するご返信には対応致しかねますのでご了承下さい。
@endcomponent
