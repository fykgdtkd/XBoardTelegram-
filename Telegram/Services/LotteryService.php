<?php

namespace Plugin\Telegram\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Plugin\Telegram\Plugin;

class LotteryService
{
    public function __construct(protected Plugin $plugin) {}

    public function getTodayStatus(int $userId, string $telegramId): array
    {
        $today = Carbon::today()->toDateString();

        $userUsed = (int) DB::table('plugin_telegram_lottery_logs')
            ->where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->count();

        $tgUsed = (int) DB::table('plugin_telegram_lottery_logs')
            ->where('telegram_id', $telegramId)
            ->whereDate('created_at', $today)
            ->count();

        $userLimit = (int) $this->plugin->getConfig('lottery_daily_limit_per_user', 1);
        $tgLimit   = (int) $this->plugin->getConfig('lottery_daily_limit_per_telegram', 1);

        return [
            'user_used' => $userUsed,
            'tg_used' => $tgUsed,
            'user_left' => max(0, $userLimit - $userUsed),
            'tg_left' => max(0, $tgLimit - $tgUsed),
            'user_limit' => $userLimit,
            'tg_limit' => $tgLimit,
        ];
    }

    public function drawForUser(User $user, string $telegramId): array
    {
        $minMb = max(1, (int) $this->plugin->getConfig('lottery_min_mb', 1));
        $maxMb = max($minMb, (int) $this->plugin->getConfig('lottery_max_mb', 1024));

        $status = $this->getTodayStatus((int) $user->id, $telegramId);

        if ($status['user_left'] <= 0) {
            return ['ok' => false, 'message' => "⏳ 今日抽奖次数已用完（每日 {$status['user_limit']} 次）"];
        }
        if ($status['tg_left'] <= 0) {
            return ['ok' => false, 'message' => "⏳ 该 Telegram 今日抽奖次数已用完（每日 {$status['tg_limit']} 次）"];
        }

        $rewardMb = random_int($minMb, $maxMb);
        $bytes = $rewardMb * 1024 * 1024;

        DB::transaction(function () use ($user, $telegramId, $rewardMb, $bytes) {
            DB::table('plugin_telegram_lottery_logs')->insert([
                'user_id' => $user->id,
                'telegram_id' => $telegramId,
                'reward_mb' => $rewardMb,
                'reward_bytes' => $bytes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ✅ 不写死 users / v2_user，直接用模型自增（最稳）
            $user->increment('transfer_enable', $bytes);
        });

        return [
            'ok' => true,
            'reward_mb' => $rewardMb,
            'reward_bytes' => $bytes,
            'message' => "🎉 抽奖成功！你获得：{$rewardMb}MB 流量\n已自动发放到账号额度（transfer_enable）✅"
        ];
    }
}
