# Gandi provider

## Configuration

1. Add to `.env` file required envrionment variables with your own **credentials** :

 ```
 GANDI_API_KEY=
 ```

2. Update **service** definition to wiring `AliasApiInterface` with `GandiAliasApi`.

3. Reload cache and voila.
