<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('البريد')
                    ->searchable(),

                TextColumn::make('roles.name')
                    ->label('الدور')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Manager' => 'success',
                        'Staff' => 'info',
                        default => 'gray',
                    }),

                IconColumn::make('restaurants.pivot.is_active')
                    ->label('نشط؟')
                    ->boolean()
                    // لعرض حالة التفعيل الخاصة بهذا المطعم تحديداً
                    ->getStateUsing(function (User $record) {
                        return $record->restaurants()
                            ->where('restaurant_id', Filament::getTenant()->id)
                            ->first()
                            ->pivot
                            ->is_active ?? false;
                    }),

                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (User $record) {
                        // عند الحذف، نفصل العلاقة مع المطعم بدلاً من حذف المستخدم نهائياً
                        // لأن المستخدم قد يكون موظفاً في مطعم آخر
                        $record->restaurants()->detach(Filament::getTenant()->id);
                    }),
            ])
            ->bulkActions([
                // Bulk Delete disabled for safety in logic
            ]);
    }
}
