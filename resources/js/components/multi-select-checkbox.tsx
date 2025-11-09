import { cn } from '@/lib/utils';

export function MultiSelectCheckbox({ item, selectedIds, onToggle }) {
    const isSelected = selectedIds.includes(item.id);
    return (
        <label
            className={cn(
                'flex cursor-pointer items-center space-x-2 rounded-md border p-3 transition-colors',
                isSelected ? 'border-primary bg-primary/10' : 'hover:bg-muted/50'
            )}
        >
            <input
                type="checkbox"
                checked={isSelected}
                onChange={() => onToggle(item.id)}
                className="form-checkbox h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
            />
            <span className="text-sm font-medium">{item.name}</span>
        </label>
    );
}
