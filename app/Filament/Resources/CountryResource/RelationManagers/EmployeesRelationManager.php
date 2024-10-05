<?php

namespace App\Filament\Resources\CountryResource\RelationManagers;

use App\Models\City;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Employee location')                         
                ->schema([
                    Forms\Components\Select::make('country_id')
                        ->relationship(name: 'country', titleAttribute: 'name')
                        ->native(false)
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(
                            function (Set $set){
                                $set('state_id', null);
                                $set('city_id', null);
                            } 
                        )
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('state_id')
                        ->options(
                            fn(Get $get): SupportCollection => State::query()->where('country_id', $get('country_id'))->pluck('name', 'id')
                        )
                        ->native(false)
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(
                            fn(Set $set) => $set('city_id', null)
                            )
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('city_id')
                        ->options(
                            fn(Get $get): SupportCollection => City::query()->where('state_id', $get('state_id'))->pluck('name', 'id')
                        )
                        ->native(false)
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('department_id')
                        ->relationship(name: 'department', titleAttribute: 'name')
                        ->native(false)
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpanFull(),
                    ])->columns(3),
                Forms\Components\Section::make('User Name')
                ->description('Put user name details in.')                
                ->schema([
                    Forms\Components\TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('middle_name')
                        ->required()
                        ->maxLength(255)])->columns(3)
                ,
                Forms\Components\Section::make('User Address')                    
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('zip_code')
                            ->required()
                            ->maxLength(255)
                    ])->columns(2)
                ,
                Forms\Components\Section::make('Dates')                    
                    ->schema([
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('date_of_hired')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])->columns(2)                                
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                Tables\Columns\TextColumn::make('state.name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),              
                Tables\Columns\TextColumn::make('city.name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('department.name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),                    
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),                    
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('zip_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_of_hired')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
