<?php

namespace App\Filament\Pages;

use App\Models\Content\SiteSetting;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Contenu du site';

    protected static ?string $navigationLabel = 'Réglages du site';

    protected static ?string $title = 'Réglages du site';

    protected static string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::current()->toArray();

        // Le Repeater "simple" attend un tableau d'objets { text: ... }, pas
        // un tableau de chaînes brutes (format plus simple à parcourir côté
        // Blade public) — conversion aller-retour ici, jamais en base.
        $settings['trust_items'] = collect($settings['trust_items'] ?? [])
            ->map(fn (string $text) => ['text' => $text])
            ->all();

        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Bandeau de réassurance')
                    ->description("Les 3 phrases affichées sous les nouveautés de l'accueil.")
                    ->schema([
                        Repeater::make('trust_items')
                            ->label('Phrases')
                            ->simple(TextInput::make('text')->required()->maxLength(255))
                            ->maxItems(3)
                            ->minItems(1)
                            ->reorderable(false),
                    ]),

                Section::make('Page À propos')
                    ->schema([
                        Textarea::make('about_story')
                            ->label('Histoire de la marque')
                            ->rows(6)
                            ->helperText('Un saut de ligne double sépare les paragraphes.'),

                        TextInput::make('about_quote')
                            ->label('Citation'),

                        Repeater::make('about_values')
                            ->label('Valeurs')
                            ->schema([
                                TextInput::make('title')->label('Titre')->required()->maxLength(255),
                                TextInput::make('text')->label('Description')->required()->maxLength(255),
                            ])
                            ->columns(2)
                            ->maxItems(4)
                            ->reorderable(false),
                    ]),

                Section::make('Coordonnées de contact')
                    ->schema([
                        TextInput::make('contact_whatsapp')->label('WhatsApp'),
                        TextInput::make('contact_email')->label('Email')->email(),
                        TextInput::make('contact_address')->label('Adresse'),
                        Textarea::make('contact_hours')->label('Horaires / livraison')->rows(3),
                    ])
                    ->columns(2),

                Section::make('Pied de page & réseaux sociaux')
                    ->description('Icônes affichées uniquement si le lien est renseigné.')
                    ->schema([
                        TextInput::make('footer_tagline')->label('Accroche sous le logo')->maxLength(255),
                        TextInput::make('social_instagram')->label('Instagram (URL)')->url()->maxLength(255),
                        TextInput::make('social_facebook')->label('Facebook (URL)')->url()->maxLength(255),
                        TextInput::make('social_tiktok')->label('TikTok (URL)')->url()->maxLength(255),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $state['trust_items'] = collect($state['trust_items'] ?? [])
            ->pluck('text')
            ->filter()
            ->values()
            ->all();

        SiteSetting::current()->update($state);

        Notification::make()->title('Réglages enregistrés')->success()->send();
    }
}
