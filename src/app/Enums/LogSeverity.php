<?php

namespace App\Enums;

enum LogSeverity: string
{
    /** ログ エントリには重大度レベルが割り当てられていません。 */
    case DEFAULT = 'default';
    /** デバッグまたはトレース情報。 */
    case DEBUG = 'debug';
    /** 進行中のステータスやパフォーマンスなどの日常的な情報。 */
    case INFO = 'info';
    /** 通常だが重要なイベント (起動、シャットダウン、構成変更など)。 */
    case NOTICE = 'notice';
    /** 警告イベントは問題を引き起こす可能性があります。 */
    case WARNING = 'warning';
    /** エラー イベントは問題を引き起こす可能性があります。 */
    case ERROR = 'error';
    /** 重大なイベントは、より深刻な問題や機能停止を引き起こします。 */
    case CRITICAL = 'critical';
    /** 人は直ちに行動を起こさなければなりません。 */
    case ALERT = 'alert';
    /** 1 つ以上のシステムが使用できません。 */
    case EMERGENCY = 'emergency';
}