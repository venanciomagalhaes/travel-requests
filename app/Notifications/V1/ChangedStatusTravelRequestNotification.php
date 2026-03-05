<?php

namespace App\Notifications\V1;

use App\Enums\V1\TravelRequest\TravelRequestStatusEnum;
use App\Models\TravelRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChangedStatusTravelRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Número de vezes que a notificação deve ser tentada em caso de erro.
     */
    public $tries = 5;

    /**
     * Número de segundos a aguardar antes de tentar novamente (backoff progressivo).
     */
    public $backoff = [60, 120, 240];

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly User $user,
        private readonly TravelRequest $travelRequest,
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->getStatusLabel();

        $departureDate = Carbon::parse($this->travelRequest->departure_date)->format('d/m/Y');
        $returnDate = Carbon::parse($this->travelRequest->return_date)->format('d/m/Y');

        return (new MailMessage)
            ->subject('Atualização no Status da sua Viagem')
            ->greeting("Olá, {$this->user->name}!")
            ->line("O status do seu pedido de viagem para **{$this->travelRequest->destination}** foi atualizado.")
            ->line('### Detalhes do Período:')
            ->line("- **Partida:** {$departureDate}")
            ->line("- **Retorno:** {$returnDate}")
            ->line('---')
            ->line("Novo Status: **{$statusLabel}**")
            ->line('Obrigado por utilizar nossa aplicação!');
    }

    public function toArray(object $notifiable): array
    {
        $departureDate = Carbon::parse($this->travelRequest->departure_date)->format('d/m/Y');
        $statusLabel = $this->getStatusLabel();

        return [
            'type' => 'status_change',
            'travel_request_uuid' => $this->travelRequest->uuid,
            'user_uuid' => $this->user->uuid,
            'user_name' => $this->user->name,
            'status_label' => $statusLabel,
            'content' => [
                'destination' => $this->travelRequest->destination,
                'departure_date' => $this->travelRequest->departure_date,
                'return_date' => $this->travelRequest->return_date,
                'status' => $this->travelRequest->status,
            ],
            'message' => "O status da sua viagem para {$this->travelRequest->destination} ({$departureDate}) mudou para {$statusLabel}.",
        ];
    }

    private function getStatusLabel(): string
    {
        return match ($this->travelRequest->status) {
            TravelRequestStatusEnum::APPROVED->value => 'Aprovado',
            TravelRequestStatusEnum::CANCELED->value => 'Cancelado',
            default => $this->travelRequest->status->value,
        };
    }
}
