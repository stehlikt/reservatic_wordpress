# Reservatic WordPress Plugin

Tento plugin umožňuje integraci Reservatic API do vašeho WordPress webu.

## Požadavky

- PHP verze 8.3 nebo vyšší

## Instalace

1. Stáhněte ZIP soubor s pluginem.
2. Přihlaste se do administrace WordPressu.
3. Přejděte na **Pluginy** > **Přidat nový**.
4. Klikněte na **Nahrát plugin** a vyberte stažený ZIP soubor.
5. Klikněte na **Instalovat nyní** a poté na **Aktivovat**.

## Nastavení

1. V levém menu se objeví **Reservatic Plugin**.
2. Klikněte na **Reservatic Plugin** > **Nastavení**.
3. Vyplňte následující údaje:
   - **Res URL**: Například `https://api-dev.reservatic.com/api/profi`
   - **API Token**
   - Nahrajte certifikát
   - Nastavte heslo k certifikátu
4. Klikněte na **Uložit změny**.

## Vytvoření formuláře

1. Přejděte na **Reservatic Plugin** > **Formuláře**.
2. Klikněte na **Vytvořit nový formulář**.
3. Nastavte následující údaje:
   - **Služba**: Vyberte službu, pro kterou chcete formulář nastavit.
   - **Vzhled formuláře**:
     - Barva prvků
     - Barva textu tlačítek
     - Zaoblení rohů
     - Barva pozadí
     - Barva textu
     - Zobrazit logo Reservatic
     - Zobrazit GooglePlay a AppStore ikony
4. Klikněte na **Vytvořit**.

## Použití formuláře

1. Po vytvoření formuláře se vygeneruje shortcode ve tvaru `[reservatic_form_id]`, například `[reservatic_form_1]`.
2. Tento shortcode vložte na jakoukoliv stránku vašeho webu, kde chcete zobrazit Reservatic formulář.

## Podpora

Pokud máte nějaké dotazy nebo problémy, můžete mě kontaktovat na [podpora@reservatic.com].
