(function () {
  Craft.BaseElementIndex.prototype._showExportHud = function () {
    this.$exportBtn.addClass('active');
    this.$exportBtn.attr('aria-expanded', 'true');

    const $form = $('<form/>', {
      class: 'export-form',
    });

    const typeOptions = [];
    for (let i = 0; i < this.exporters.length; i++) {
      typeOptions.push({
        label: this.exporters[i].name,
        value: this.exporters[i].type,
      });
    }

    const $typeField = Craft.ui
      .createSelectField({
        label: Craft.t('app', 'Export Type'),
        options: typeOptions,
        class: 'fullwidth',
      })
      .appendTo($form);

    const $formatField = Craft.ui
      .createSelectField({
        label: Craft.t('app', 'Format'),
        options: [
          { label: 'CSV', value: 'csv' },
          { label: 'JSON', value: 'json' },
          { label: 'XML', value: 'xml' },
          { label: 'XLSX', value: 'xlsx' },
        ],
        class: 'fullwidth',
      })
      .appendTo($form);

    const $typeSelect = $typeField.find('select');
    this.addListener($typeSelect, 'change', () => {
      const type = $typeSelect.val();
      if (this.exportersByType[type].formattable) {
        $formatField.removeClass('hidden');
      } else {
        $formatField.addClass('hidden');
      }
    });
    $typeSelect.trigger('change');

    const selectedElementIds = this.view.getSelectedElementIds();

    let $limitField;
    if (!selectedElementIds.length) {
      $limitField = Craft.ui
        .createTextField({
          label: Craft.t('app', 'Limit'),
          placeholder: Craft.t('app', 'No limit'),
          type: 'number',
          min: 1,
        })
        .appendTo($form);
    }

    const $submitBtn = Craft.ui
      .createSubmitButton({
        class: 'fullwidth',
        label: Craft.t('app', 'Export'),
        spinner: true,
      })
      .appendTo($form);

    const $exportSubmit = new Garnish.MultiFunctionBtn($submitBtn);

    const hud = new Garnish.HUD(this.$exportBtn, $form);

    hud.on('hide', () => {
      this.$exportBtn.removeClass('active');
      this.$exportBtn.attr('aria-expanded', 'false');
    });

    let submitting = false;

    this.addListener($form, 'submit', function (ev) {
      ev.preventDefault();
      if (submitting) {
        return;
      }

      submitting = true;
      $exportSubmit.busyEvent();

      const params = this.getViewParams();
      delete params.baseCriteria.offset;
      delete params.baseCriteria.limit;
      delete params.criteria.offset;
      delete params.criteria.limit;
      delete params.collapsedElementIds;

      params.type = $typeField.find('select').val();
      params.format = $formatField.find('select').val();

      if (selectedElementIds.length) {
        params.criteria.id = selectedElementIds;
      } else {
        const limit = parseInt($limitField?.find('input').val());
        if (limit && !isNaN(limit)) {
          params.criteria.limit = limit;
        } else {
          // don't set the default limit of 100
          delete params.criteria.limit;
        }
      }

      if (Craft.csrfTokenValue) {
        params[Craft.csrfTokenName] = Craft.csrfTokenValue;
      }

      Craft.downloadFromUrl(
        'POST',
        Craft.getActionUrl('element-indexes/export'),
        params
      )
        .catch((e) => {
          if (!axios.isCancel(e)) {
            Craft.cp.displayError(e?.response?.data?.message);
          }
        })
        .finally(() => {
          submitting = false;
          $exportSubmit.successEvent();
        });
    });
  };
})();
