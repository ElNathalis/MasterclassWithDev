# CI/CD Pipeline

Пайплайн запускается при push или PR в ветки main, develop, uat.

## Шаги
1. **Тесты** с покрытием ≥50%
2. **PHPStan** – статический анализ
3. **Laravel Pint** – проверка стиля (для долгоживущих веток) или автоисправление (для остальных)
4. **Симуляция деплоя**:
   - develop → .env.dev
   - uat → .env.uat
   - main → .env.prod (требует ручного аппрува через GitHub Environments)
5. **Уведомление** maintainers (через комментарий)

Для продакшн-деплоя настроен environment `production` с required reviewers.